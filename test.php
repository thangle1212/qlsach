<?php

echo password_verify(
    '1234',
    '1234'
) ? 'OK' : 'FAIL';