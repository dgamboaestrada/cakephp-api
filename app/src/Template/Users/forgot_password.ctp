<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= __('¿Olvidaste tu contraseña?') ?></h3>
            </div>
            <div class="panel-body">
                <?= $this->Form->create() ?>
                    <fieldset>
                        <div class="form-group">
                            <? echo $this->Form->input('username', ['label' => __('Escribe tu usuario'), 'class' => 'form-control' ,'placeholder' => __('Usuario del sistema'), 'required' => 'required']); ?>
                        </div>
                        <?= $this->Form->button(__('Recuperar contraseña'), ['class' => 'btn btn-lg btn-success btn-block']); ?>
                    </fieldset>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?= $this->Flash->render('error') ?>
    </div>
</div>
