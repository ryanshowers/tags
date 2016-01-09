@extends('app')

@if($tag)

@section('meta_title', trans('tags::messages.show.meta.title', ['tag' => $tag->name]))
@section('meta_description', trans('tags::messages.show.meta.description', ['tag' => $tag->name]))
@section('meta_keywords', trans('tags::messages.show.meta.keywords', ['items' => implode(',', array_pluck($tag->pages, 'title'))]))

@section('content')

<section id="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
			    <h1 class="page-header">
			        {!! trans(
    			         'tags::messages.show.title',
    			         ['tag' => '<em class="text-muted">' . $tag->name . '</em>']
                    ) !!}
                </h1>
			</div>
		</div>
		<div class="row">
		    <div class="col-xs-12">
    		    @if ($pages->isEmpty())
    		    <p>{{ trans('tags::messages.empty', ['tag' => $tag->name]) }}</p>
    		    @else
                <div class="list-group">
    		    @foreach ($pages as $page)
    		        <a href="{!! route(config('pages.route') . '.show', $page->slug); !!}" class="list-group-item">{{ $page->title }}
    		        </a>
                @endforeach
                </div>
                {!! $pages->render() !!}
    		    @endif
		    </div>
		</div>
	</div>
</section>
@stop

@else

@section('content')
<section id="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
			    <h1 class="page-header">{{ trans('tags::messages.missing') }}</h1>
			</div>
		</div>
	</div>
</section>
@stop

@endif