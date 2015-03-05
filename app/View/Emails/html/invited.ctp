<h3><?= __('Welcome, you have been invited to join the project %s at %s. Please activate your account.', $project['Project']['name'], console::$systemName) ?></h3>
Just click on this link: <?=$this->Html->link('Activate account', $activateLink)?><br /><br />
Then you'll be able to login with <?= $email ?> and password: <?=$password?> at the <?=$this->Html->link('login page', $loginLink)?><br /><br />
Please change your password under settings after login.<br /><br />
You have been invited by <?=$authUser['email']?>.
<?= console::$systemName ?>