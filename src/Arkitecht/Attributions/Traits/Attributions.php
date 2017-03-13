<?php
namespace Arkitecht\Attributions\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;

trait Attributions
{
    protected $attributions_user_class = 'App\User';

    /**
     * Boot the attributions trait for a model.
     * If softdeletes is enabled for the model, add a delete listener to update the deleter attribute
     *
     * @return void
     */
    public static function bootAttributions()
    {
        self::deleted(function ($model) {
            if ($model->usesSoftDeletes()) {
                $model->updateDeleterAttribution();
            }
        });
    }

    /**
     * Perform the insert, and update the creator and updater attributions
     *
     * @param Builder $query
     * @param array   $options
     *
     * @return mixed
     */
    protected function performInsert(Builder $query, array $options = [])
    {
        $this->updateAttributions();

        return parent::performInsert($query, $options);
    }

    /**
     * Perform the update, and update the creator and updater attributions
     *
     * @param Builder $query
     * @param array   $options
     *
     * @return mixed
     */
    protected function performUpdate(Builder $query, array $options = [])
    {
        $this->updateAttributions();

        return parent::performUpdate($query, $options);
    }

    /**
     * Update the updater_id and creator_id of the model
     *
     * @return void
     */
    public function updateAttributions()
    {
        $this->updater_id = $this->getAttribution();
        if (!$this->exists) {
            $this->creator_id = $this->getAttribution();
        }
    }

    /**
     * Update the deleter attribution of the model
     *
     * @return void
     */
    public function updateDeleterAttribution()
    {
        if ($this !== null && $this->trashed()) {
            $query = $this->newQueryWithoutScopes();
            $this->deleter_id = $this->getAttribution();
            $saved = $this->performUpdate($query, []);
        }
    }

    /**
     * Get the logged in user or null for the attribution
     *
     * @return null
     */
    public function getAttribution()
    {
        if (auth()->user())
            return auth()->user()->id;

        return null;
    }

    /**
     * Get the creator of the model, based on the relationship
     *
     * @return mixed
     */
    public function creator()
    {
        return $this->belongsTo($this->attributions_user_class, 'creator_id');
    }

    /**
     * Get the updater of the model, based on the relationship
     *
     * @return mixed
     */
    public function updater()
    {
        return $this->belongsTo($this->attributions_user_class, 'updater_id');
    }

    /**
     * Get the deleter of the model, based on the relationship
     *
     * @return mixed
     */
    public function deleter()
    {
        return $this->belongsTo($this->attributions_user_class, 'deleter_id');
    }

    /**
     * Does this model use the SoftDeletes trait?
     *
     * @return bool
     */
    private function usesSoftDeletes()
    {
        $traits = class_uses(self::class);

        return in_array('Illuminate\Database\Eloquent\SoftDeletes', $traits);
    }
}
