<?php
class ProductController extends Controller
{
    private const PER_PAGE = 5;

    public function __construct() { $this->requireLogin(); }

    public function index(): void
    {
        $q    = Validator::string($_GET['q'] ?? '', 0, 100) ?? '';
        $page = Validator::int($_GET['page'] ?? 1, 1) ?? 1;
        [$rows, $total] = Product::paginate($q, $page, self::PER_PAGE);
        $this->render('products/index', [
            'title' => 'Products', 'products' => $rows, 'q' => $q,
            'page' => $page, 'pages' => max(1, (int)ceil($total / self::PER_PAGE)),
        ]);
    }

    public function create(): void
    {
        $this->render('products/form', ['title' => 'Add product', 'product' => [], 'errors' => [], 'action' => '/products']);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        [$data, $errors] = Product::validate($_POST);
        [$image, $upErr] = Uploader::save($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        if ($upErr) $errors['image'] = $upErr;

        if ($errors) {
            Uploader::delete($image);
            $this->render('products/form', ['title' => 'Add product', 'product' => $_POST,
                'errors' => $errors, 'action' => '/products'], 422);
            return;
        }
        $data['image'] = $image;
        Product::create($data);
        Session::flash('success', 'Product added.');
        $this->redirect('/products');
    }

    public function edit(string $id): void
    {
        $product = Product::find((int)$id) ?? $this->missing();
        $this->render('products/form', ['title' => 'Edit product', 'product' => $product,
            'errors' => [], 'action' => "/products/{$product['id']}/update"]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $product = Product::find((int)$id) ?? $this->missing();
        [$data, $errors] = Product::validate($_POST);
        [$image, $upErr] = Uploader::save($_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        if ($upErr) $errors['image'] = $upErr;

        if ($errors) {
            Uploader::delete($image);
            $this->render('products/form', ['title' => 'Edit product', 'product' => $_POST + ['id' => $id],
                'errors' => $errors, 'action' => "/products/$id/update"], 422);
            return;
        }
        if ($image) { Uploader::delete($product['image']); $data['image'] = $image; }
        Product::update((int)$id, $data);
        Session::flash('success', 'Product updated.');
        $this->redirect('/products');
    }

    public function destroy(string $id): void
    {
        $this->verifyCsrf();
        $product = Product::find((int)$id) ?? $this->missing();
        Product::delete((int)$id);
        Uploader::delete($product['image']);
        Session::flash('success', 'Product deleted.');
        $this->redirect('/products');
    }

    private function missing(): never
    {
        (new ErrorController())->notFound();
        exit;
    }
}
