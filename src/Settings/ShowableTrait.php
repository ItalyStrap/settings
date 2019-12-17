<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

trait ShowableTrait {

	/**
	 * Show on page
	 *
	 * @param string|bool $condition The config array.
	 * @return bool         Return true if conditions are resolved.
	 */
	private function showOn( $condition ): bool {

		if ( \is_bool( $condition ) ) {
			return $condition;
		}

		if ( \is_callable( $condition ) ) {
			return (bool) \call_user_func( $condition );
		}

		return false;
	}
}
