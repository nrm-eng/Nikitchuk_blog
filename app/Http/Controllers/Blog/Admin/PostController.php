<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Models\BlogPost;
use App\Http\Requests\BlogPostCreateRequest;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogCategoryRepository;
use App\Http\Requests\BlogPostUpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends BaseController
{
       /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;

    /**
     * @var BlogCategoryRepository
     */
    private $blogCategoryRepository; // властивість через яку будемо звертатись в репозиторій категорій

    public function __construct()
    {
        parent::__construct();
        $this->blogPostRepository = app(BlogPostRepository::class); //app вертає об'єкт класа
        $this->blogCategoryRepository = app(BlogCategoryRepository::class);
    }

    public function index()
    {
        $paginator = $this->blogPostRepository->getAllWithPaginate();
        return view('blog.admin.posts.index', compact('paginator'));
    }

     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
          $item = new BlogPost();
        $categoryList = $this->blogCategoryRepository->getForComboBox();
        

        return view('blog.admin.posts.edit', compact('item', 'categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogPostCreateRequest $request)
    {
         $data = $request->input(); //отримаємо масив даних, які надійшли з форми

        $item = (new BlogPost())->create($data); //створюємо об'єкт і додаємо в БД

        if ($item) {
            return redirect()
                ->route('blog.admin.posts.edit', [$item->id])
                ->with(['success' => 'Успішно збережено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Помилка збереження'])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $item = $this->blogPostRepository->getEdit($id);
        if (empty($item)) {
            abort(404); // якщо не знайдено
        }

        $categoryList = $this->blogCategoryRepository->getForComboBox();

        return view('blog.admin.posts.edit', compact('item', 'categoryList'));
    }

    /**
     * Оновлення поста
     */
    public function update(BlogPostUpdateRequest $request, $id)
    {
        $item = $this->blogPostRepository->getEdit($id);
        if (empty($item)) {
            return back()
                ->withErrors(['msg' => "Запис id=[{$id}] не знайдено"])
                ->withInput();
        }

        $data = $request->all();

        $result = $item->update($data);

        if ($result) {
            return redirect()
                ->route('blog.admin.posts.edit', $item->id)
                ->with(['success' => 'Успішно збережено']);
        } else {
            return back()
                ->with(['msg' => 'Помилка збереження'])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
         $result = BlogPost::destroy($id); //софт деліт, запис лишається

        //$result = BlogPost::find($id)->forceDelete(); //повне видалення з БД

        if ($result) {
            return redirect()
                ->route('blog.admin.posts.index')
                ->with(['success' => "Запис id[$id] видалено"]);
        } else {
            return back()
                ->withErrors(['msg' => 'Помилка видалення']);
        }
    }
}
