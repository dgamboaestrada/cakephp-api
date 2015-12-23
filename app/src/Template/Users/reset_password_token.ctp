<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= __('Escribe tu nueva contraseña') ?></h3>
            </div>
            <div class="panel-body">
                <?= $this->Form->create() ?>
                    <fieldset>
                        <? echo $this->Form->input('reset_password_token', ['type' => 'hidden', 'value' => $token]); ?>
                        <div class="form-group">
                            <? echo $this->Form->input('new_password', ['type' => 'password', 'label' => __('Nueva contraseña'), 'class' => 'form-control', 'placeholder' => __('Nueva contraseña'), 'required' => 'required']); ?>
                        </div>
                        <div class="form-group">
                            <? echo $this->Form->input('repeat_password', ['type' => 'password', 'label' => __('Contraseña'), 'class' => 'form-control', 'placeholder' => __('Confirma la contraseña'), 'required' => 'required']); ?>
                        </div>
                        <?= $this->Form->button(__('Cambiar contraseña'), ['class' => 'btn btn-lg btn-success btn-block']); ?>
                    </fieldset>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?= $this->Flash->render('error') ?>
        <?= $this->Flash->render('success') ?>
    </div>
</div>
