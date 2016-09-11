<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Topic\TopicRepository;
use App\Repositories\Reply\ReplyRepository;
use Illuminate\Support\Facades\Auth;
use App\Services\Transformer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Input;

/**
 * Class TopicController
 * @package App\Http\Controllers\Api
 */
class TopicController extends ApiController
{

    /**
     * Shows a topic.
     *
     * @param TopicRepository $topic
     * @param null            $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Topic", description="Get all or one topic")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/api/v1/topics/{id?}")
     * @ApiParams(name="id", type="integer", nullable=true, description="topic id")
     */
    public function topics(TopicRepository $topic, $id = null)
    {
        $topics = $this->getCollection($topic, $id);

        Transformer::walker($topics);

        return $this->response([$this->stringData => $topics], 200);
    }

    /**
     * Creating or update a topic.
     *
     * @param TopicRepository $topic
     * @param ReplyRepository $reply
     * @param null            $id
     *
     * @return mixed
     */
    private function createOrUpdateTopic(TopicRepository $topic, ReplyRepository $reply, $id = null)
    {
        if ( ! is_null($id)) {
            $currentTopic = $topic->get($id);
            $replies      = $currentTopic->replies;
            $user_id      = $replies[0]->user_id;
            if ($user_id != Auth::user()->id && ! Auth::user()->hasPermission('create_topic', false)) {
                return $this->response([$this->stringErrors => [$this->stringUser => 'You have not that created that topic']],
                    400);
            }
        }
        $input = Input::all();
        if ($topic->createOrUpdate(Input::all(), $id)) {
            $input['topic_id'] = $topic->topic->id;
            if (is_null($id) && ! $reply->createOrUpdate($input)) {
                $topic->delete($topic->topic->id);

                return $this->response([$this->stringErrors => $reply->getErrors()], 400);
            }

            return $this->response([$this->stringMessage => 'Your topic has been saved'], 201);
        }

        return $this->response([$this->stringErrors => $topic->getErrors()], 400);
    }

    /**
     * Update a topic.
     *
     * @param TopicRepository $topic
     * @param ReplyRepository $reply
     * @param null            $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Topic", description="Update topic")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/api/v1/topics/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="topic id")
     * @ApiParams(name="title", type="string", nullable=false, description="topic title")
     * @ApiParams(name="forum_id", type="integer", nullable=false, description="forum id")
     */
    public function updateTopic(TopicRepository $topic, ReplyRepository $reply, $id)
    {
        return $this->createOrUpdateTopic($topic, $reply, $id);
    }

    /**
     * Update a topic.
     *
     * @param TopicRepository $topic
     * @param ReplyRepository $reply
     *
     * @return mixed
     *
     * @ApiDescription(section="Topic", description="Create topic")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/topics")
     * @ApiParams(name="title", type="string", nullable=false, description="topic title")
     * @ApiParams(name="forum_id", type="integer", nullable=false, description="forum id")
     * @ApiParams(name="reply", type="string", nullable=false, description="reply")
     * @ApiParams(name="user_id", type="integer", nullable=true, description="user id")
     */
    public function createTopic(TopicRepository $topic, ReplyRepository $reply)
    {
        return $this->createOrUpdateTopic($topic, $reply);
    }

    /**
     * Delets a topic.
     * @permission delete_topic:optional
     *
     * @param TopicRepository $topicRepository
     * @param                 $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Topic", description="Delete topic")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/api/v1/topics/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="topic id")
     */
    public function deleteTopic(TopicRepository $topicRepository, $id)
    {
        try {
            $topic = $topicRepository->get($id);
            if ( ! is_null($topic)) {
                $reply = $topic->replies()->first();
                if (Auth::user()
                        ->hasPermission($this->getPermission(), false) || Auth::user()->id == $reply->user_id
                ) {
                    if ($topicRepository->delete($id)) {
                        return $this->response([$this->stringMessage => 'Your topic has been deleted.'], 200);
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $this->response([$this->stringErrors => 'That topic could not be deleted.'], 204);
    }
}
