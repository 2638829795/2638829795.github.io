<?php
if (!defined('puyuetian')) {
	exit('403');
}

$_G['SET']['EMBED_HEAD'] .= template('puyuetian_search:embed', true);
