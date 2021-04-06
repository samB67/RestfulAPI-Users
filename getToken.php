<?php

echo base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());