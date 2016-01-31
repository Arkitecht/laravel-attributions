<?php
namespace Arkitecht\Attributions\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;

trait Attributions
{
    protected $attributions_user_class = 'App\User';

    protected function performInsert(Builder $query, array $options = [])
    {
        $this->updateAttributions();

        return parent::performInsert($query, $options);
    }

    protected function performUpdate(Builder $query, array $options = [])
    {
        $this->updateAttributions();

        return parent::performUpdate($query, $options);
    }

    public function updateAttributions()
    {
        $this->updater_id = $this->getAttribution();
        if (!$this->exists) {
            $this->creator_id = $this->getAttribution();
        }
    }

    public function getAttribution()
    {
        if (auth()->user())
            return auth()->user()->id;

        return null;
    }

    public function creator()
    {
        return $this->belongsTo($this->attributions_user_class,'creator_id');
    }

    public function updater()
    {
        return $this->belongsTo($this->attributions_user_class,'updater_id');
    }

}