<?php

namespace App\Helpers;

interface IWebservice {

	public function prepareAPIRequest($instance, $emails = "", $is_mass_request = false);
}