@extends('master')

@section('css')

@stop

@section('content')
	<h2>
		{{$topic->title}}
		@if(count($topic->replies) > 0 && Auth::check() && Auth::user()->id == $topic->replies->first()->user_id)
			<a class="float-right" href="{{URL::action('TopicController@delete', array($topic->id))}}"><i class="fa fa-trash-o"></i></a>
		@endif
	</h2>
	@if(count($topic->replies) > 0)
		@if(Auth::check() && Auth::user()->id == $topic->replies->first()->user_id)
			<div class="margin-bottom-one margin-top-one inline-group-form">
				{{ Form::model($topic, array('action' => array('TopicController@createOrUpdate', $topic->id))) }}
				{{ Form::label('title','Topic title') }}
				{{ Form::text('title', Input::old('Title'), array('Placeholder' => 'Topic title')) }}
				{{ $errors->first('title', '<div class="alert error">:message</div>') }}
				{{ Form::button('Update', array('type' => 'submit')) }}
				{{ Form::close() }}
			</div>
		@endif
		<div class="forum">
			<div class="item invert">
				<div class="content">
					<div class="avatar">Avatar</div>
					<div class="content">Reply</div>
				</div>
			</div>
			@foreach($topic->replies as $reply)
				<div class="item">
					<a class="avatar" href="/user/{{$reply->user->username}}"><img alt="Avatar for {{$reply->user->username}}" src="{{HTML::avatar($reply->user->id)}}"></a>
					<div class="reply">
						<p class="font-bold">
							<a href="/user/{{$reply->user->username}}">{{$reply->user->username}}</a>,
							{{$reply->created_at->diffForHumans()}}
							@if(Auth::check() && Auth::user()->id == $reply->user_id)
								<span class="float-right">
									<a href="{{URL::action('TopicController@show', array($topic->id, $reply->id))}}"><i class="fa fa-pencil"></i></a>
									@if($topic->replies->first()->id != $reply->id)
										<a href="{{URL::action('ReplyController@delete', array($reply->id))}}"><i class="fa fa-trash-o"></i></a>
									@endif
								</span>
							@endif
						</p>
						<p>{{HTML::mention(HTML::markdown($reply->reply))}}</p>
					</div>
				</div>
			@endforeach
		</div>
	@else
		<div class="alert info">This topic have no replies yet.</div>
	@endif
	@if(is_null($editReply))
		{{ Form::model($editReply, array('action' => 'ReplyController@createOrUpdate')) }}
	@else
		{{ Form::model($editReply, array('action' => array('ReplyController@createOrUpdate', $editReply->id))) }}
	@endif
	<h3 class="margin-top-one">Reply</h3>
	{{ Form::hidden('topic_id', Route::Input('id')) }}
	{{ Form::label('reply', 'Reply:') }}
	{{ Form::textarea('reply', Input::old('reply'), array('placeholder' => 'Your reply', 'class' => 'mentionarea', 'id' => 'reply', 'data-validator' => 'required|min:3')) }}
	{{ $errors->first('reply', '<div class="alert error">:message</div>') }}
	@if(!is_null($editReply))
		<a class="float-left font-bold" href="/forum/topic/{{$editReply->topic->id}}">Cancel edit of reply</a>
	@endif
	{{ Form::button('Reply', array('type' => 'submit')) }}
	{{ Form::close() }}
@stop

@section('script')

@stop                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       