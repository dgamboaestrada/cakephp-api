<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= __('Acceso') ?></h3>
            </div>
            <div class="panel-body">
                <?= $this->Form->create() ?>
                    <fieldset>
                        <div class="form-group">
                            <? echo $this->Form->input('username', ['label' => __('Usuario'), 'class' => 'form-control' ,'placeholder' => __('Usuario del sistema')]); ?>
                        </div>
                        <div class="form-group">
                            <? echo $this->Form->input('password', ['label' => __('Contraseña'), 'class' => 'form-control', 'placeholder' => __('Contraseña de acceso al sistema')]); ?>
                        </div>
                        <?= $this->Form->button(__('Iniciar sesión'), ['class' => 'btn btn-lg btn-success btn-block']); ?>
                        <div class="form-group">
                            <?php
                                echo $this->Html->link(
                                __('¿Olvidaste tu contraseña?'),
                                ['controller' => 'Users', 'action' => 'forgotPassword']
                            ); ?>
                        </div>
                    </fieldset>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?= $this->Flash->render('error') ?>
        <?= $this->Flash->render('success') ?>
    </div>
</div>
