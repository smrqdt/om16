<?php

class LoginController extends Controller {

    public function index(){
        if ($this->app->request()->isPost()) {
            $v = $this->validator($this->post());
            $v->rule('required', array('email', 'password'));
            $v->rule('length', 'email', 4, 22);
            $v->rule('length', 'password', 3, 11);
            if ($v->validate()) {
                if ($this->auth->login($this->post('email'), $this->post('password'))) {
                    $this->app->flash('info', 'Your login was successfull');
                    $u = $this->auth->getUser();
                    $user = User::find($u["id"]);
                    if($user->admin > 0){
                    	$this->redirect("admin");
                    }else{
                    	$this->redirect('home');
                    }
                }
            }
            $this->app->flashNow('error', $this->errorOutput($v->errors()));
        }
        $this->render('login/login.tpl');
    }

    public function signup(){
    	print_r($_SESSION);
        if ($this->app->request()->isPost()) {
            $v = $this->validator($this->post());
            $v->rule('required', array('email', 'username', 'password', 'password_verify', 'address'));
            $v->rule('email', 'email');
            $v->rule('length', 'username', 4, 22);
            $v->rule('length', 'password', 3, 11);
            $v->rule('equals', 'password', 'password_verify');
            if ($v->validate()) {
            	$u = new User();
                $u->name = $this->post('name');
                $u->email = $this->post('email');
                $u->username = $this->post('username');
                $u->address = $this->post('address');
                $u->password = $this->auth->getProvider()->hashPassword($this->post('password'));
                $u->save();

                $this->app->flash('info', 'Your registration was successfull');
                $this->auth->login($this->post('username'), $this->post('password'), $this->post('remember'));
                $this->redirect('home');
            }
            $this->app->flash('error', $this->errorOutput($v->errors()));
        }
        $this->render('login/signup.tpl');
    }

    public function logout(){
        $this->app->flash('info', 'Come back sometime soon');
        $this->auth->logout(true);
        $this->redirect('home');
    }

    public function forgot(){
        if ($this->auth->loggedIn()) {
            $this->redirect('/', false);
        }
        $this->render('login/forgot');
    }
}