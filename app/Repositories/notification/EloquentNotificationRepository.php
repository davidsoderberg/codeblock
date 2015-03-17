<?php namespace App\Repositories\Notification;

use App\Notification;
use App\NotificationType;
use App\Repositories\CRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;

class EloquentNotificationRepository extends CRepository implements NotificationRepository {

	private $user;

	public function __construct(UserRepository $user){
		$this->user = $user;
	}

	public function send($user_id, $type, $subject, $body, $object) {

		$note = new Notification();

		if(is_numeric($user_id)){
			$note->user_id = $user_id;
		}

		if(isset($type) && NotificationType::isType($type)){
			$note->type = $type;
		}

		if(isset($subject)){
			$note->subject;
		}

		if(isset($body)){
			$note->body;
		}

		$namespaces = explode('\\', get_class($object));
		$object_type = $namespaces[count($namespaces)-1];
		if(is_object($object) && class_exists('App\\'.$object_type)){
			$note->object_id = $object->id;
			$note->object_type = $object_type;
		}

		$note->sent_at = new \DateTime('now');
		$note->from_id = Auth::user()->id;

		if($note->save()){
			return $this->sendNotification($note);
		}else{
			$this->errors = $note::$errors;
			return false;
		}
	}

	private function sendNotification($notification){
		if($notification->body != ''){
			$body = $notification->body;
		}else{
			switch($notification->type){
				default:
					return false;
					break;
			}
		}

		$user = $this->user->get($notification->user_id);
		if(!is_null($user)) {
			$data = array('subject' => $notification->subject, 'body' => $body);
			$emailInfo = array('toEmail' => $user->email, 'toName' => $user->username, 'subject' => $notification->subject);
			if($this->sendEmail('emails.notification', $emailInfo, $data) == 1) {
				return true;
			}
		}
		return false;
	}
}