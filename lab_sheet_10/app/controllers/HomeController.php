<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $this->render('home/index', ['title' => 'Home']);
    }

    public function dashboard(): void
    {
        $this->requireLogin();
        $this->render('home/dashboard', ['title' => 'Dashboard', 'productCount' => Product::count()]);
    }

    public function apiClient(): void
    {
        $this->render('apiclient/index', ['title' => 'API client']);
    }
}
