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
        $this->render('login.tpl');
    }

    public function signup(){
        if ($this->app->request()->isPost()) {
            $v = $this->validator($this->post());
            $v->rule('required', array('email', 'username', 'password'));
            $v->rule('email', 'email');
            $v->rule('length', 'username', 4, 22);
            $v->rule('length', 'password', 3, 11);
            if ($v->validate()) {
                $u = R::dispense('users');
                $u->name = $this->post('name');
                $u->email = $this->post('email');
                $u->username = $this->post('username');
                $u->password = $this->auth->getProvider()->hashPassword($this->post('password'));
                $u->ip_address = $this->app->request()->getIp();
                R::store($u);

                $this->app->flash('info', 'Your registration was successfull');
                $this->redirect('home');
            }
            $this->app->flashNow('error', $this->errorOutput($v->errors()));
        }
        $this->render('login/signup');
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