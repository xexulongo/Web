<p><?= sprintf(Yii::t('engrescat', 'Hola %s!'), CHtml::encode($name)) ?>,</p>
<p><?= Yii::t('engrescat', "Ens ha arribat una petició per recuperar la teva contrasenya al portal Engresca't. Si us plau, per continuar amb el procés, fes click al següent enllaç:")?></p>
<p><a href="<?= Yii::app()->createAbsoluteUrl('user/forgot', array('token' => $token, 'email' => $email)); ?>"><?= Yii::app()->createAbsoluteUrl('user/activate', array('token' => $token, 'email' => $email)); ?></a></p>