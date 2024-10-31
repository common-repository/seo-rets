<?php

$this->set_session_data('user_index', false);

header("Location: " . get_bloginfo('url') . "/sr-login");

exit;
