<?php

namespace Ryanshowers\Tags;

use Illuminate\Database\Eloquent\Model;
use DB;

class Tag extends Model
{
    
    
    /**
     * Page polymorphic relationship
     * 
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function pages() {
        return $this->morphedByMany('Ryanshowers\Pages\Page', 'taggable');
    }
    
    public function scopePublic($query) {
        return $query->where('public', '=', '1');
    }       
    
    /**
     * Scope popular tags
     * 
     * @access public
     * @param mixed $query
     * @return Query
     */
    public function scopePopular($query) {
        return $query->select(DB::raw('*, count(taggable_id) as number_of_tags'))
            ->join('taggables', 'tags.id', '=', 'taggables.tag_id')
            ->groupBy('tag_id')
            ->orderBy('number_of_tags', 'DESC');        
    }
    
    
    /**
     * Scope popular tags with pages relationship
     * 
     * @access public
     * @param mixed $query
     * @return Query
     */
    public function scopePopularInPages($query) {
        return $query->with(['pages' => function($q) {
            return $q->select(DB::raw('count(taggable_id) as number_of_tags'))->groupBy('taggable_id');
        }]);
    }
}
