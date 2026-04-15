<?php

/**
 * Model Class - Base class cho tất cả models
 * 
 * Cung cấp các phương thức tiện ích để tương tác với database
 * 
 * Cách sử dụng:
 * 
 *   class Book extends Model {
 *       protected $table = 'books';
 *       protected $fillable = ['title', 'author', 'isbn'];
 *   }
 * 
 *   // Lấy
 *   $book = Book::find(1);
 *   $books = Book::all();
 *   
 *   // Tìm theo điều kiện
 *   $book = Book::where('isbn', '123456')->first();
 *   $books = Book::where('category', 'Fiction')->get();
 *   
 *   // Tạo
 *   $book = Book::create(['title' => '...', 'author' => '...']);
 *   
 *   // Cập nhật
 *   $book->update(['title' => 'New Title']);
 *   
 *   // Xóa
 *   $book->delete();
 */

class Model
{

    /**
     * Tên bảng database
     * PHẢI override ở child class
     * 
     * @var string
     */
    protected $table;

    /**
     * Primary key column
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Các column có thể fill hàng loạt (fillable)
     * Chỉ những column này mới được phép gán từ bên ngoài
     * 
     * @var array
     */
    protected $fillable = [];

    /**
     * Các column không được fill (guarded)
     * 
     * @var array
     */
    protected $guarded = [];

    /**
     * Tự thêm timestamps (created_at, updated_at)
     * 
     * @var bool
     */
    protected $timestamps = true;

    /**
     * Tên column created_at
     * 
     * @var string
     */
    protected $createdAtColumn = 'created_at';

    /**
     * Tên column updated_at
     * 
     * @var string
     */
    protected $updatedAtColumn = 'updated_at';

    /**
     * Hỗ trợ soft delete (đánh dấu deleted_at)
     * 
     * @var bool
     */
    protected $softDelete = false;

    /**
     * Tên column deleted_at (cho soft delete)
     * 
     * @var string
     */
    protected $deletedAtColumn = 'deleted_at';

    /**
     * Dữ liệu model instance
     * 
     * @var array
     */
    protected $attributes = [];

    /**
     * Database connection
     * 
     * @var PDO
     */
    protected static $db;

    /**
     * Constructor
     * 
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        static::$db = \Database::getInstance();
        $this->fill($attributes);
    }

    /**
     * Lấy tên bảng
     * 
     * @return string
     */
    public function getTable()
    {
        return $this->table ?: strtolower(class_basename($this)) . 's';
    }

    /**
     * Lấy primary key column
     * 
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Gán attributes hàng loạt
     * 
     * @param array $attributes
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Kiểm tra column có được phép fill không
     * 
     * @param string $key
     * @return bool
     */
    protected function isFillable($key)
    {
        // Nếu có guarded, check xem key có trong guarded
        if (!empty($this->guarded)) {
            return !in_array($key, $this->guarded);
        }

        // Nếu fillable không rỗng, check xem key có trong fillable
        if (!empty($this->fillable)) {
            return in_array($key, $this->fillable);
        }

        // Mặc định không fill
        return false;
    }

    /**
     * Magic: Lấy attribute
     * 
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic: Gán attribute
     * 
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Lấy tất cả attributes
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * ===== STATIC METHODS - Query =====
     */

    /**
     * Lấy tất cả records
     * 
     * @return array
     */
    public static function all()
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();

        $query = "SELECT * FROM $table";

        // Nếu soft delete, loại bỏ deleted records
        if ($instance->softDelete) {
            $deletedAtColumn = $instance->deletedAtColumn;
            $query .= " WHERE $deletedAtColumn IS NULL";
        }

        $stmt = $db->prepare($query);
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new static($row);
        }

        return $results;
    }

    /**
     * Lấy record theo ID
     * 
     * @param mixed $id
     * @return static|null
     */
    public static function find($id)
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();
        $pk = $instance->getPrimaryKey();

        $query = "SELECT * FROM $table WHERE $pk = ?";

        // Nếu soft delete, loại bỏ deleted records
        if ($instance->softDelete) {
            $deletedAtColumn = $instance->deletedAtColumn;
            $query .= " AND $deletedAtColumn IS NULL";
        }

        $stmt = $db->prepare($query);
        $stmt->execute([$id]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new static($row) : null;
    }

    /**
     * Lấy first record theo điều kiện
     * 
     * @param string $column - Column name
     * @param mixed $value - Column value
     * @return static|null
     */
    public static function findBy($column, $value)
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();

        $query = "SELECT * FROM $table WHERE $column = ?";

        // Nếu soft delete
        if ($instance->softDelete) {
            $deletedAtColumn = $instance->deletedAtColumn;
            $query .= " AND $deletedAtColumn IS NULL";
        }

        $stmt = $db->prepare($query);
        $stmt->execute([$value]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new static($row) : null;
    }

    /**
     * Lấy tất cả records theo điều kiện
     * 
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public static function findAllBy($column, $value)
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();

        $query = "SELECT * FROM $table WHERE $column = ?";

        if ($instance->softDelete) {
            $deletedAtColumn = $instance->deletedAtColumn;
            $query .= " AND $deletedAtColumn IS NULL";
        }

        $stmt = $db->prepare($query);
        $stmt->execute([$value]);

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new static($row);
        }

        return $results;
    }

    /**
     * Tạo record mới
     * 
     * @param array $data
     * @return static
     */
    public static function create($data)
    {
        $instance = new static($data);
        $instance->save();
        return $instance;
    }

    /**
     * ===== INSTANCE METHODS - Save, Update, Delete =====
     */

    /**
     * Lưu model vào database
     * Nếu có ID → update, không có → insert
     * 
     * @return bool
     */
    public function save()
    {
        $db = static::$db;
        $table = $this->getTable();
        $pk = $this->getPrimaryKey();

        // Thêm timestamps
        if ($this->timestamps) {
            if (!isset($this->attributes[$pk])) {
                // Insert: thêm created_at
                $this->attributes[$this->createdAtColumn] = date('Y-m-d H:i:s');
            }
            // Update: cập nhật updated_at
            $this->attributes[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        // Nếu có ID → Update
        if (isset($this->attributes[$pk])) {
            return $this->update();
        }

        // Insert
        $columns = array_keys($this->attributes);
        $columnsStr = implode(', ', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $query = "INSERT INTO $table ($columnsStr) VALUES ($placeholders)";

        $stmt = $db->prepare($query);
        $result = $stmt->execute(array_values($this->attributes));

        if ($result) {
            $this->attributes[$pk] = $db->lastInsertId();
        }

        return $result;
    }

    /**
     * Cập nhật model hiện tại
     * 
     * @return bool
     */
    protected function update()
    {
        $db = static::$db;
        $table = $this->getTable();
        $pk = $this->getPrimaryKey();
        $pkValue = $this->attributes[$pk];

        // Cập nhật updated_at
        if ($this->timestamps) {
            $this->attributes[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        // Tạo SET clause
        $sets = [];
        foreach ($this->attributes as $column => $value) {
            if ($column !== $pk) {
                $sets[] = "$column = ?";
            }
        }

        if (empty($sets)) {
            return true;
        }

        $setsStr = implode(', ', $sets);
        $query = "UPDATE $table SET $setsStr WHERE $pk = ?";

        // Chuẩn bị values
        $values = [];
        foreach ($this->attributes as $column => $value) {
            if ($column !== $pk) {
                $values[] = $value;
            }
        }
        $values[] = $pkValue;

        $stmt = $db->prepare($query);
        return $stmt->execute($values);
    }

    /**
     * Cập nhật model từ data mới
     * 
     * @param array $data
     * @return bool
     */
    public function updateWith($data)
    {
        $this->fill($data);
        return $this->save();
    }

    /**
     * Xóa record (hard delete hoặc soft delete)
     * 
     * @return bool
     */
    public function delete()
    {
        $db = static::$db;
        $table = $this->getTable();
        $pk = $this->getPrimaryKey();
        $pkValue = $this->attributes[$pk] ?? null;

        if (!$pkValue) {
            return false;
        }

        // Soft delete: đặt deleted_at = now
        if ($this->softDelete) {
            $deletedAtColumn = $this->deletedAtColumn;
            $query = "UPDATE $table SET $deletedAtColumn = ? WHERE $pk = ?";
            $stmt = $db->prepare($query);
            return $stmt->execute([date('Y-m-d H:i:s'), $pkValue]);
        }

        // Hard delete: xóa hoàn toàn
        $query = "DELETE FROM $table WHERE $pk = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$pkValue]);
    }

    /**
     * Khôi phục record đã soft delete
     * 
     * @return bool
     */
    public function restore()
    {
        if (!$this->softDelete) {
            return false;
        }

        $db = static::$db;
        $table = $this->getTable();
        $pk = $this->getPrimaryKey();
        $pkValue = $this->attributes[$pk] ?? null;

        if (!$pkValue) {
            return false;
        }

        $deletedAtColumn = $this->deletedAtColumn;
        $query = "UPDATE $table SET $deletedAtColumn = NULL WHERE $pk = ?";

        $stmt = $db->prepare($query);
        return $stmt->execute([$pkValue]);
    }

    /**
     * Xóa vĩnh viễn (dù là soft delete)
     * 
     * @return bool
     */
    public function forceDelete()
    {
        $db = static::$db;
        $table = $this->getTable();
        $pk = $this->getPrimaryKey();
        $pkValue = $this->attributes[$pk] ?? null;

        if (!$pkValue) {
            return false;
        }

        $query = "DELETE FROM $table WHERE $pk = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$pkValue]);
    }

    /**
     * Đếm records
     * 
     * @return int
     */
    public static function count()
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();

        $query = "SELECT COUNT(*) as count FROM $table";

        if ($instance->softDelete) {
            $deletedAtColumn = $instance->deletedAtColumn;
            $query .= " WHERE $deletedAtColumn IS NULL";
        }

        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)($result['count'] ?? 0);
    }

    /**
     * Kiểm tra record có tồn tại không
     * 
     * @param string $column
     * @param mixed $value
     * @return bool
     */
    public static function exists($column, $value)
    {
        return static::findBy($column, $value) !== null;
    }

    /**
     * Xóa hàng loạt theo điều kiện
     * 
     * @param string $column
     * @param mixed $value
     * @return int - Số rows bị xóa
     */
    public static function deleteWhere($column, $value)
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();

        $query = "DELETE FROM $table WHERE $column = ?";

        $stmt = $db->prepare($query);
        $stmt->execute([$value]);

        return $stmt->rowCount();
    }

    /**
     * Cập nhật hàng loạt
     * 
     * @param string $whereColumn
     * @param mixed $whereValue
     * @param array $data
     * @return int - Số rows bị cập nhật
     */
    public static function updateWhere($whereColumn, $whereValue, $data)
    {
        $instance = new static();
        $db = static::$db;
        $table = $instance->getTable();

        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $sets[] = "$column = ?";
            $values[] = $value;
        }

        $values[] = $whereValue;

        $setsStr = implode(', ', $sets);
        $query = "UPDATE $table SET $setsStr WHERE $whereColumn = ?";

        $stmt = $db->prepare($query);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    /**
     * Chuyển đổi sang array
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Chuyển đổi sang JSON
     * 
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->attributes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Lấy helper function - tên class (singular)
     */
    protected static function className()
    {
        $class = get_called_class();
        return substr($class, strrpos($class, '\\') + 1);
    }

    /**
     * Helper: Basename
     */
    public static function getBaseName()
    {
        return class_basename(get_called_class());
    }
}

/**
 * Helper function - lấy basename class
 */
if (!function_exists('class_basename')) {
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}
