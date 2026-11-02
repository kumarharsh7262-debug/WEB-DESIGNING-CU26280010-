<?php
class AuthController extends Controller
{
    /* ---------------- Register ---------------- */
    public function registerForm(): void
    {
        $this->render('auth/register', ['title' => 'Register', 'old' => [], 'errors' => []]);
    }

    public function register(): void
    {
        $this->verifyCsrf();                                              // Q9
        $username = Validator::string($_POST['username'] ?? '', 3, 50);   // Q11
        $email    = Validator::email($_POST['email'] ?? '');
        $pass     = (string)($_POST['password'] ?? '');
        $errors   = [];

        if ($username === null || !preg_match('/^[A-Za-z0-9_]+$/', $username)) $errors['username'] = 'Username: 3-50 letters, digits or underscore.';
        if ($email === null)        $errors['email'] = 'Enter a valid e-mail address.';
        if (strlen($pass) < 8)      $errors['password'] = 'Password must be at least 8 characters.';
        if ($pass !== ($_POST['confirm'] ?? null)) $errors['confirm'] = 'Passwords do not match.';
        if (!$errors && User::findByEmail($email)) $errors['email'] = 'This e-mail is already registered.';

        if ($errors) {
            $this->render('auth/register', [
                'title' => 'Register', 'errors' => $errors,
                'old' => ['username' => $username ?? '', 'email' => $email ?? ''],
            ], 422);
            return;
        }

        User::create([
            'username' => $username, 'email' => $email,
            'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
        ]);
        Mailer::welcome($email, $username);                               // Q14
        Session::flash('success', 'Registration successful. Please log in.');
        $this->redirect('/login');
    }

    /* ---------------- Login (Q10 + Q12) ---------------- */
    public function loginForm(): void
    {
        $this->render('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $this->verifyCsrf();
        $email = Validator::email($_POST['email'] ?? '');
        $pass  = (string)($_POST['password'] ?? '');
        $user  = $email ? User::findByEmail($email) : null;

        if ($user && ($mins = User::lockMinutesLeft($user)) > 0) {        // Q12: locked
            $this->render('auth/login', ['title' => 'Login',
                'error' => "Too many failed attempts. Account locked - try again in $mins minute(s)."], 429);
            return;
        }

        if ($user && password_verify($pass, $user['password_hash'])) {
            User::clearFailures((int)$user['id']);
            Session::regenerate();                                        // Q10: new session ID after login
            Session::set('user_id', (int)$user['id']);
            Session::set('username', $user['username']);
            Session::flash('success', 'Welcome back, ' . $user['username'] . '!');
            $this->redirect('/dashboard');
        }

        if ($user) User::recordFailure($user);
        $this->render('auth/login', ['title' => 'Login', 'error' => 'Invalid e-mail or password.'], 401);
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Session::destroy();
        Session::start();
        Session::flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }

    /* ---------------- Forgot / reset password (Q13) ---------------- */
    public function forgotForm(): void
    {
        $this->render('auth/forgot', ['title' => 'Forgot password']);
    }

    public function forgot(): void
    {
        $this->verifyCsrf();
        $email = Validator::email($_POST['email'] ?? '');
        if ($email && ($user = User::findByEmail($email))) {
            $token = User::createResetToken((int)$user['id']);
            $link  = rtrim(config('app.base_url'), '/') . '/reset-password/' . $token;
            Mailer::passwordReset($email, $link);                         // Q14
        }
        // Same message whether or not the account exists (prevents user enumeration).
        Session::flash('success', 'If that e-mail is registered, a reset link has been sent.');
        $this->redirect('/login');
    }

    public function resetForm(string $token): void
    {
        if (!User::findByResetToken($token)) {
            $this->render('auth/reset_invalid', ['title' => 'Link expired'], 400);
            return;
        }
        $this->render('auth/reset', ['title' => 'Reset password', 'token' => $token, 'error' => null]);
    }

    public function reset(): void
    {
        $this->verifyCsrf();
        $token = (string)($_POST['token'] ?? '');
        $user  = User::findByResetToken($token);
        if (!$user) {
            $this->render('auth/reset_invalid', ['title' => 'Link expired'], 400);
            return;
        }
        $pass = (string)($_POST['password'] ?? '');
        if (strlen($pass) < 8 || $pass !== ($_POST['confirm'] ?? null)) {
            $this->render('auth/reset', ['title' => 'Reset password', 'token' => $token,
                'error' => 'Passwords must match and be at least 8 characters.'], 422);
            return;
        }
        User::update((int)$user['id'], [
            'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
            'reset_token_hash' => null, 'reset_expires' => null,           // one-time use
            'failed_attempts' => 0, 'locked_until' => null,
        ]);
        Session::flash('success', 'Password updated. Please log in.');
        $this->redirect('/login');
    }
}
