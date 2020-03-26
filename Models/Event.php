<?php

namespace App\Modules\Event\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Tags\HasTags;
use Illuminate\Support\Facades\Log;

class Event extends Model implements Searchable
{
	use LogsActivity, HasTags;

    protected $fillable = ['name', 'description', 'address', 'price', 'datetime', 'external_link', 'image_url'];

    protected $appends = ['free'];

    public function getImageAttribute()
	{
		if ($this->image_url != null)
	   		return $this->image_url;
	   	else 
	   		return '/img/event-placeholder.jpeg';
	}

	public function getFreeAttribute()
	{
		if ($this->price == null || $this->price == 0)
			return true;
		else
			return false;
	}

	public function getDateStartAttribute()
	{
		if ($this->datetime != null)
	    	return Carbon::parse($this->datetime)->format('Y-m-d\TH:i');
	    else
	    	return null;
	}

	public function getDisplayDateAttribute()
	{
		// Sat, March 7, 2020

		if ($this->datetime != null)
	    	return Carbon::parse($this->datetime)->format('D, M d, Y g:i A');
	    else
	    	return null;
	}

	public function getPriceDisplayAttribute()
	{
		if ($this->price == null || $this->price == 0)
			return 'Free';
		else
			return '$'.number_format($this->price,0);
	}

	public function getTags()
	{
		return implode(", ", $this->tags->pluck('name')->toArray());
	}

	public function getSearchResult(): SearchResult
    {
        $url = route('events.show', $this->id);

        return new SearchResult(
            $this,
            $this->name,
            $url
        );
    }

}
