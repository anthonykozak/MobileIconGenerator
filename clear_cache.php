<?php
array_map('unlink', glob("cache/*"));
array_map('unlink', glob("uploads/*"));
?>