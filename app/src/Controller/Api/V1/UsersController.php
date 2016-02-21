<?php
namespace App\Controller\Api\V1;

use App\Controller\Api\V1\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Roles']
        ];
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        throw new NotFoundException;
        return;
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        throw new NotFoundException;
        return;
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        throw new NotFoundException;
        return;
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Open session.
     * @return
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Usuario o contraseña inválido.'), [ 'key' => 'error', ]);
        }
    }

    /**
     * Close session.
     * @return
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Allow a user to request a password reset.
     * @return
     */
    function forgotPassword()
    {
        if ($this->request->is('post')) {
            $username =  $this->request->data['username'];
            $usersTable = TableRegistry::get('Users');
            $user = $usersTable->find("username", ['username' => $username])->first();
            if (!count($user)) {
                $this->Flash->error(__('El usuario no existe.'), [ 'key' => 'error', ]);
                $this->redirect('/users/forgot_password');
            } else {
                $user = $this->generatePasswordToken($user);
                if ($usersTable->save($user) && $this->sendForgotPasswordEmail($user->id)) {
                    $this->Flash->success(
                        __('Ha sido enviado a su correo electrónico instrucciones para restablecer
                        su contraseña. Tienes 24 horas para completar la solicitud.'),
                        [ 'key' => 'success', ]);
                    $this->redirect('/users/login');
                }
            }
        }
    }

    /**
     * Allow user to reset password if $token is valid.
     * @return
     */
    function resetPasswordToken($resetPasswordToken = null)
    {
        $usersTable = TableRegistry::get('Users');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $usersTable->find("resetPasswordToken", ['resetPasswordToken' => $this->request->data['reset_password_token']])->first();
            if (!empty($user->reset_password_token) && !empty($user->token_created_at) && $this->validToken($user->token_created_at)) {
                if ($this->request->data['new_password'] != $this->request->data['repeat_password']) {
                    $this->set('token', $resetPasswordToken);
                    $this->Flash->error(__('La confirmación de la contraseña no coincide.'),
                        [ 'key' => 'error', ]);
                } elseif (empty($this->request->data['new_password'])) {
                    $this->set('token', $resetPasswordToken);
                    $this->Flash->error( __('La contraseña es requerida.'), [ 'key' => 'error', ]);
                } else {
                    $user->password = $this->request->data['new_password'];
                    $user->reset_password_token = null;
                    $user->token_created_at = null;
                    if ($this->Users->save($user)) {
                        if ($this->Users->save($user) && $this->sendPasswordChangedEmail($user->id)) {
                            $this->Flash->success(
                                __('Tu contraseña ha sido cambiada satisfactoriamente. Inicia sesión para continuar.'), [
                                    'key' => 'success',
                                ]);
                            $this->redirect('/users/login');
                        }
                    }
                }
            }
        } else {
            $user = $usersTable->find('resetPasswordToken', ['resetPasswordToken' => $resetPasswordToken])->first();
            if (!empty($user->reset_password_token) && !empty($user->token_created_at) && $this->validToken($user->token_created_at)) {
                $this->set('token', $resetPasswordToken);
            } else {
                $this->Flash->error(
                    __('Este enlace a expirado o no es válido.'), [
                        'key' => 'error',
                    ]);
                $this->redirect('/users/login');
            }
        }
    }

    /**
     * Generate a unique hash / token.
     * @param Object User
     * @return Object User
     */
    private function generatePasswordToken($user)
    {
        if (empty($user)) {
            return null;
        }
        // Generate a random string 100 chars in length.
        $token = "";
        for ($i = 0; $i < 100; $i++) {
            $d = rand(1, 100000) % 2;
            $d ? $token .= chr(rand(33,79)) : $token .= chr(rand(80,126));
        }
        (rand(1, 100000) % 2) ? $token = strrev($token) : $token = $token;
        // Generate hash of random string
        $hash = Security::hash($token, 'sha256', true);;
        for ($i = 0; $i < 20; $i++) {
            $hash = Security::hash($hash, 'sha256', true);
        }
        $user->reset_password_token = $hash;
        $user->token_created_at = date('Y-m-d H:i:s');
        return $user;
    }
    /**
     * Validate token created at time.
     * @param String $token_created_at
     * @return Boolean
     */
    private function validToken($token_created_at)
    {
        $expired = strtotime($token_created_at) + 86400;
        $time = strtotime("now");
        if ($time < $expired) {
            return true;
        }
        return false;
    }

    /**
     * Sends password reset email to user's email address.
     * @param $id
     * @return
     */
    private function sendForgotPasswordEmail($id = null)
    {
        if (!empty($id)) {
            $user = $this->Users->get($id, [
                'contain' => []
            ]);
            $email = new Email();
            $email->template('reset_password_request')
                ->emailFormat('html')
                ->to($user->email)
                ->from(__('do-not-reply@lavadero.com'))
                ->subject(__('Password Reset Request - DO NOT REPLY'))
                ->set('user',$user)
                ->send();
            return true;
        }
        return false;
    }

    /**
     * Notifies user their password has changed.
     * @param $id
     * @return
     */
    private function sendPasswordChangedEmail($id = null)
    {
        if (!empty($id)) {
            $user = $this->Users->get($id, [
                'contain' => []
            ]);
            $email = new Email();
            $email->template('password_reset_success')
                ->emailFormat('html')
                ->to($user->email)
                ->from(__('do-not-reply@lavadero.com'))
                ->subject(__('Password Reset success - DO NOT REPLY'))
                ->set('user',$user)
                ->send();
            return true;
        }
        return false;
    }
}
