<?php

namespace Ryanshowers\Tags;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Ryanshowers\Tags\Tag;
use Ryanshowers\Pages\Page;
use Illuminate\Support\Facades\Input;

class TagController extends Controller
{
    
    public function __construct() {
        
        $this->middleware('web');
        
    	$this->middleware('auth', [
    	    'except' => ['index', 'show']
        ]);
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
		
		//Do we have a search query?
		if ($q = Input::get('q')) {
			$tags = Tag::where('name', 'LIKE', '%'.$q.'%')->paginate(20);
		} else {
			$tags = Tag::popular()->public()->paginate(config('tags.pagination'));
		} 
		
		return $request->wantsJson() ? $tags : view('tags::index', [
			'tags' => $tags,
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $name = strtolower(trim($request->input('name')));
        $tag = Tag::firstOrCreate([
            'name' => $name,
            'slug' => str_slug($name, '-'),
            'public' => 1
        ]);
        
        return $tag;
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $slug
     * @return Response
     */
    public function show($slug)
    {
        /*$tag = Tag::with(['pages' => function($query) {
            $query->orderBy('title', 'ASC');
            $query->paginate(5);
        }])->where('slug','=', $slug)->first();*/
        
        $tag = Tag::where('slug', '=', $slug)->first();
        $pages = Page::whereHas('tags', function($query) use ($tag) {
            $query->where('id', '=', $tag->id);
        })->orderBy('title', 'ASC')->paginate(config('tags.pagination'));
        
		return view('tags::show', [
			'tag' => $tag,
			'pages' => $pages
		]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if ($tag = Tag::where('id', '=', $id)->first()) {
            $tag->public = $request->input('public');
            $tag->update();
            $tag->touch();
            return $tag;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
