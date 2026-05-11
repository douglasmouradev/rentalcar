<?php

declare(strict_types=1);

final class UserController
{
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $p = User::paginated($page, $perPage);
        View::render('users/index', [
            'title' => Lang::get('nav.users'),
            'users' => $p['rows'],
            'pagination' => $p,
            'paginationBase' => Router::url('/users'),
            'listQuery' => [],
        ], 'main');
    }

    public function createForm(): void
    {
        View::render('users/create', ['title' => Lang::get('user.create')], 'main');
    }

    public function create(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/users/create'));
            exit;
        }
        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'role' => in_array($_POST['role'] ?? '', ['owner', 'operator'], true) ? $_POST['role'] : 'operator',
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'is_active' => !empty($_POST['is_active']) ? 1 : 0,
            'lang_pref' => in_array($_POST['lang_pref'] ?? '', ['pt-BR', 'en-US'], true) ? $_POST['lang_pref'] : 'pt-BR',
        ];
        if (strlen($data['password']) < 8) {
            Flash::error(Lang::get('user.password_short'));
            header('Location: ' . Router::url('/users/create'));
            exit;
        }
        try {
            $id = User::create($data);
        } catch (Throwable $e) {
            Flash::error(Lang::get('flash.error'));
            header('Location: ' . Router::url('/users/create'));
            exit;
        }
        Audit::log(Auth::id(), 'create', 'user', $id, null, ['email' => $data['email'], 'role' => $data['role']]);
        Flash::success(Lang::get('flash.saved'));
        header('Location: ' . Router::url('/users'));
        exit;
    }
}
