<?php
declare(strict_types=1);

use ItalyStrap\HTML\Attributes as ATTR;

return [
	[
		'label'			=> 'Label',
		'name'			=> __( 'Custom Title', 'italystrap' ),
		'desc'			=> __( 'If test mode is active the front-end form on submit will return an array with som edefault values.', 'italystrap' ),
		'id'			=> 'custom',
		'type'			=> 'color',
		'value'			=> true,
		'sanitize'		=> 'sanitize_text_field',
	],
];
