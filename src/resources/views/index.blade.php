@extends('layouts.app')
	
@section('meta_title', trans('tags::messages.index.meta.title'))
@section('meta_description', trans('tags::messages.index.meta.description'))
@section('meta_keywords', trans('tags::messages.index.meta.keywords', ['tags' => implode(',', array_pluck($tags, 'name'))]))

@section('content')

<section id="content">
	<div class="container-fluid">
	    <div class="row">
	        <div class="col-xs-12">
	            <h1 class="page-header">{{ trans('tags::messages.index.title') }}</h1>
	            <div class="list-group">
	            @foreach ($tags as $tag)
	                <a href="{!! route(config('tags.route') . '.show', $tag->slug); !!}" class="list-group-item">
	                    {{ $tag->name }} <span class="badge">{{ trans_choice('tags::messages.badge', $tag->number_of_tags) }}</span>
	                </a>
	            @endforeach
	            </div>
	        </div>
        </div>
        {!! $tags->render() !!}
	</div>
</section>
@stop