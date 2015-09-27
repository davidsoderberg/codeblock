<?php namespace App\Repositories;

use App\Services\CacheTrait;
use Illuminate\Support\Facades\Mail;
// bas klassen för repon
Class CRepository {

	use CacheTrait;

	// fel behållaren för fel
	public $errors;

	public static $self;

	public function __construct(){
		$this->enableCaching = env('CACHE');
		Self::$self = $this;
	}

	// get metod för felen
	public function getErrors(){
		return $this->errors;
	}

	// skickar alla mejl som skickas från applikationen.
	public function sendEmail($template, $emailInfo, $data){
		Mail::send($template, $data, function($message) use ($emailInfo) {
			$message->from(env('FROM_ADRESS'), env('FROM_NAME'));
			$message->to($emailInfo['toEmail'], $emailInfo['toName'])->subject($emailInfo['subject']);
		});

		if(count(Mail::failures()) <= 0){
			return true;
		}
	}

	// används för att ta bort alla html elment från input från användaren.
	public function stripTrim($input){
		return trim(strip_tags($input));
	}

	// kollar om arrayen är en assosiativ array.
	public function is_assoc($array) {
		foreach(array_keys($array) as $key) {
			if(!is_int($key)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public static function flush($model = null){
		Self::$self->flushCache($model);
	}
}