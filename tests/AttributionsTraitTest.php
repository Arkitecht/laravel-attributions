<?php

use Arkitecht\Attributions\Database\Schema\Blueprint;
use Arkitecht\Attributions\Facades\Schema;
use Arkitecht\Attributions\Traits\Attributions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class AttributionsTraitTest extends \Orchestra\Testbench\TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadLaravelMigrations();
        $this->user = TestUser::create(['name' => 'Aaron', 'email' => 'aaron@arkideas.com', 'password' => 'test']);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model'  => TestUser::class,
        ]);
    }

    /** @test */
    function it_sets_attributions_on_model_creation()
    {
        $this->setupPostsTable();
        Auth::loginUsingId($this->user->id);
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title'   => 'An interesting Post',
            'body'    => 'Some interesting things',
        ]);

        $this->assertEquals($this->user->id, $post->creator_id);
        $this->assertEquals($this->user->id, $post->updater_id);
    }

    /** @test */
    function it_updates_attribution_on_model_update()
    {
        $this->setupPostsTable();
        Auth::loginUsingId($this->user->id);
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title'   => 'An interesting Post',
            'body'    => 'Some interesting things',
        ]);

        $this->assertEquals($this->user->id, $post->creator_id);
        $this->assertEquals($this->user->id, $post->updater_id);

        $editingUser = TestUser::create(['name' => 'Bob', 'email' => 'bob@arkideas.com', 'password' => 'test']);
        Auth::loginUsingId($editingUser->id);
        $post->update(['title' => 'A somewhat interesting post']);

        $this->assertEquals($this->user->id, $post->fresh()->creator_id);
        $this->assertEquals($editingUser->id, $post->fresh()->updater_id);
    }

    /** @test */
    function it_updates_attributions_on_model_delete()
    {
        $this->setupPostsTable();
        Auth::loginUsingId($this->user->id);
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title'   => 'An interesting Post',
            'body'    => 'Some interesting things',
        ]);

        $this->assertEquals($this->user->id, $post->creator_id);
        $this->assertEquals($this->user->id, $post->updater_id);
        $this->assertNull($post->deleter_id);
        $this->assertNull($post->deleted_at);

        $editingUser = TestUser::create(['name' => 'Bob', 'email' => 'bob@arkideas.com', 'password' => 'test']);
        Auth::loginUsingId($editingUser->id);
        $post->delete();

        $fresh = $post->fresh();

        $this->assertNotNull($fresh->deleted_at);
        $this->assertEquals($this->user->id, $fresh->creator_id);
        $this->assertEquals($editingUser->id, $fresh->updater_id);
        $this->assertEquals($editingUser->id, $fresh->deleter_id);
    }

    /** @test */
    function it_updates_attributions_on_model_restore()
    {
        $this->setupPostsTable();
        Auth::loginUsingId($this->user->id);
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title'   => 'An interesting Post',
            'body'    => 'Some interesting things',
        ]);

        $this->assertEquals($this->user->id, $post->creator_id);
        $this->assertEquals($this->user->id, $post->updater_id);
        $this->assertNull($post->deleter_id);
        $this->assertNull($post->deleted_at);

        $editingUser = TestUser::create(['name' => 'Bob', 'email' => 'bob@arkideas.com', 'password' => 'test']);
        Auth::loginUsingId($editingUser->id);
        $post->delete();

        $fresh = $post->fresh();

        $this->assertNotNull($fresh->deleted_at);
        $this->assertEquals($this->user->id, $fresh->creator_id);
        $this->assertEquals($editingUser->id, $fresh->updater_id);
        $this->assertEquals($editingUser->id, $fresh->deleter_id);

        Auth::loginUsingId($this->user->id);
        $fresh->restore();

        $refresh = $post->fresh();
        $this->assertNull($refresh->deleted_at);
        $this->assertNull($refresh->deleter_id);
        $this->assertEquals($this->user->id, $refresh->creator_id);
        $this->assertEquals($this->user->id, $refresh->updater_id);
    }

    /** @test */
    function it_sets_attributions_without_soft_deletes_on_model_creation()
    {
        $this->setupCommentsTable();
        Auth::loginUsingId($this->user->id);
        $comment = Comment::create([
            'post_id' => 1,
            'user_id' => Auth::user()->id,
            'comment' => 'That post was just OK'
        ]);

        $this->assertEquals($this->user->id, $comment->creator_id);
        $this->assertEquals($this->user->id, $comment->updater_id);

    }

    /** @test */
    function it_updates_attribution_without_soft_deletes_on_model_update()
    {
        $this->setupCommentsTable();
        Auth::loginUsingId($this->user->id);
        $comment = Comment::create([
            'post_id' => 1,
            'user_id' => Auth::user()->id,
            'comment' => 'That post was just OK'
        ]);

        $this->assertEquals($this->user->id, $comment->creator_id);
        $this->assertEquals($this->user->id, $comment->updater_id);

        $editingUser = TestUser::create(['name' => 'Bob', 'email' => 'bob@arkideas.com', 'password' => 'test']);
        Auth::loginUsingId($editingUser->id);
        $comment->update(['comment' => 'I liked it!']);

        $this->assertEquals($this->user->id, $comment->fresh()->creator_id);
        $this->assertEquals($editingUser->id, $comment->fresh()->updater_id);
    }

    /** @test */
    function it_deletes_without_soft_deletes_on_model_delete()
    {
        $this->setupCommentsTable();
        Auth::loginUsingId($this->user->id);
        $comment = Comment::create([
            'post_id' => 1,
            'user_id' => Auth::user()->id,
            'comment' => 'That post was just OK'
        ]);
        $this->assertEquals(1,Comment::count());

        $this->assertEquals($this->user->id, $comment->creator_id);
        $this->assertEquals($this->user->id, $comment->updater_id);

        $editingUser = TestUser::create(['name' => 'Bob', 'email' => 'bob@arkideas.com', 'password' => 'test']);
        Auth::loginUsingId($editingUser->id);
        $comment->delete();

        $this->assertEquals(0,Comment::count());
    }

    private function setupPostsTable()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('body');
            $table->attributionsWithSoftDeletes();
            $table->timestamps();
        });
    }

    private function setupCommentsTable()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('post_id');
            $table->text('comment');
            $table->attributions();
            $table->timestamps();
        });
    }

}

class TestUser extends User
{
    protected $table = 'users';
    protected $guarded = [];
}

class Post extends Model
{
    protected $guarded = [];
    use SoftDeletes, Attributions;
}

class Comment extends Model
{
    protected $guarded = [];
    use Attributions;
}
