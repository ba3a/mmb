<?php
// +++++++++++ Показ/редактирование данных пользователя +++++++++++++++++++++++

// Выходим, если файл был запрошен напрямую, а не через include
if (!isset($MyPHPScript)) return;

	// объявлена в UserAction
	//	function UACanLinkEdit($pUserId, $raidId, $userId)

       // 03/04/2014  Добавил значения по умолчанию, чтобы подсказки в полях были не только при добавлении, 
        //но и при правке, если не былди заполнены поля при добавлении
	 $UserCityPlaceHolder = 'Город';
       	 $UserPhonePlaceHolder = 'Телефон';
       


         if ($viewmode == 'Add')
	 {
             // Новый пользователь 
             $pUserId = 0;
	     // Пока не делал возможности регистрировать пользователя уже авторизованному пользователю

	     // Если вернулись после ошибки переменные не нужно инициализировать
	     if ($viewsubmode == "ReturnAfterError") 
	     {

              ReverseClearArrays();

	      $UserEmail = $_POST['UserEmail'];
	      $UserName = CMmbUI::toHtml($_POST['UserName']);
	      $UserBirthYear = (int)$_POST['UserBirthYear'];
	      $UserProhibitAdd = mmb_isOn($_POST, 'UserProhibitAdd');
	      $UserCity = CMmbUI::toHtml($_POST['UserCity']);           // а что нам вообще пришло?
	      $UserPhone = CMmbUI::toHtml($_POST['UserPhone']);           // а что нам вообще пришло?
              // 03/07/2014  Добавляем анонмиов
	      $UserNoShow =  mmb_isOn($_POST, 'UserNoShow');

             } else {

	      $UserEmail = 'E-mail';
	      $UserName = 'Фамилия Имя';
	      $UserBirthYear = 'Год рождения';
	      $UserProhibitAdd = 0;
	      $UserCity =  $UserCityPlaceHolder;
	      $UserPhone =  $UserPhonePlaceHolder;
	      $UserNoShow = 0;

             }
            
            
             // Всегда разрешаем ввод нового пользователя 
	     $AllowEdit = 1;
	     // Определяем следующее действие
	     $NextActionName = 'AddUser';
             // Действие на текстовом поле по клику
	     $SaveButtonText = 'Зарегистрировать';
             // Кнопка "Сделать модератором" не выводится при добавлении пользователя
             $ModeratorButtonText = '';
             // Кнопка "Запросить слияние" не выводится при добавлении пользователя
	     $UnionButtonText = '';

         } else {

           // просмотр существующего
                //echo $viewsubmode;
		//

		$pUserId = $_REQUEST['UserId'];

		if ($pUserId <= 0)
		{
		// должны быть определены пользоатель, которого смотрят
		     return;
		}
           
		$sql = "select user_email, CASE WHEN COALESCE(u.user_noshow, 0) = 1 and user_id <> $UserId THEN '$Anonimus' ELSE u.user_name END as user_name,
		         user_birthyear, user_prohibitadd, user_city, user_phone, user_noshow, 
		         user_allowsendchangeinfo, user_allowsendorgmessages from  Users u where user_id = $pUserId";
                $row = CSql::singleRow($sql);

	        // Если вернулись после ошибки переменные не нужно инициализировать
	        if ($viewsubmode == "ReturnAfterError") 
		{

                  ReverseClearArrays();

		  $UserEmail = $_POST['UserEmail'];
		  $UserName = $_POST['UserName'];
		  $UserBirthYear = (int)$_POST['UserBirthYear'];
		  $UserProhibitAdd = mmb_isOn($_POST, 'UserProhibitAdd');
		  $UserCity = $_POST['UserCity'];
		  $UserPhone = $_POST['UserPhone'];
		  $UserNoShow =  mmb_isOn($_POST, 'UserNoShow');
		  $UserAllowChangeInfo =  mmb_isOn($_POST, 'UserAllowChangeInfo');
		  $UserAllowOrgMessages =  mmb_isOn($_POST, 'UserAllowOrgMessages');

                } else {

		  $UserEmail = $row['user_email'];  
		  $UserName = $row['user_name'];
		  $UserBirthYear = (int)$row['user_birthyear'];  
		  $UserProhibitAdd = $row['user_prohibitadd'];  
		  $UserCity = $row['user_city'];
		  $UserPhone = $row['user_phone'];
		  $UserNoShow = $row['user_noshow'];  
		  $UserAllowChangeInfo = (int)$row['user_allowsendchangeinfo'];  
		  $UserAllowOrgMessages =  (int)$row['user_allowsendorgmessages'];  

                }

	         $UserName = CMmbUI::toHtml($UserName);
	         $UserCity = CMmbUI::toHtml($UserCity);
	         $UserPhone = CMmbUI::toHtml($UserPhone);

	        $NextActionName = 'UserChangeData';
		$AllowEdit = 0;
		$SaveButtonText = 'Сохранить изменения';
		$ModeratorButtonText = 'Сделать модератором';
		$UnionButtonText = 'Запросить слияние';
		

                if (($pUserId == $UserId) || $Administrator)
		{
		  $AllowEdit = 1;
		}

	 }
         // Конец проверки действия с пользователем

         // Заменяем пустое значение на подсказку
	 if (empty($UserCity)) {$UserCity =  $UserCityPlaceHolder; } 
	 if (empty($UserPhone)) {$UserPhone =  $UserPhonePlaceHolder; } 

	 
         if ($AllowEdit == 0) 
	 {
	    $OnSubmitFunction = 'return false;';
	    $DisabledText = 'disabled';
	 } else {
	    $OnSubmitFunction = 'return ValidateUserDataForm();';
	    $DisabledText = '';
	 }



// Выводим javascrpit
?>

<!-- Выводим javascrpit -->
<script language = "JavaScript">

        // Функция проверки правильности заполнения формы
	function ValidateUserDataForm()
	{ 
		if (document.UserDataForm.UserName.value == '') 
		{
			alert('Не указано имя.');           
			return false;
		} 

		if (document.UserDataForm.UserEmail.value == '') 
		{
			alert('Не указан e-mail.');           
			return false;
		} 


		if (document.UserDataForm.UserBirthYear.value == '') 
		{
			alert('Не указан год.');           
			return false;
		} 
		
		if (!CheckEmail(document.UserDataForm.UserEmail.value)) 
		{
			alert('E-mail не проходит проверку формата.');           
			return false;
		} 
		
		document.UserDataForm.action.value = "<? echo $NextActionName; ?>";
		return true;
	}
        // Конец проверки правильности заполнения формы

	
	// Функция отправки пароля
	function NewPassword()
	{ 
		document.UserDataForm.action.value = "SendEmailWithNewPassword";
		document.UserDataForm.submit();
	}
	// 

        // Функция отмены изменения
	function Cancel()
	{ 
		document.UserDataForm.action.value = "CancelChangeUserData";
		document.UserDataForm.submit();
	}
	// 

	// Функция просмотра данных о команде
	/*function ViewTeamInfo(teamid, raidid)
	{ 
		document.UserTeamsForm.TeamId.value = teamid;
		document.UserTeamsForm.RaidId.value = raidid;
		document.UserTeamsForm.action.value = "TeamInfo";
		document.UserTeamsForm.submit();
	}*/


	// Функция создания модератора
	function MakeModerator()
	{ 
		document.UserDataForm.action.value = "MakeModerator";
		document.UserDataForm.submit();
	}
	// 

	// Функция создания запроса на объединение
	function MakeUnionRequest()
	{ 
		document.UserDataForm.action.value = "AddUserInUnion";
		document.UserDataForm.submit();
	}
	// 

	// Удалить пользователя
	function HideUser()
	{
               if  (!document.UserDataForm.UserHideConfirm.checked)
	       {
	         document.UserDataForm.UserHideConfirm.disabled = false;
	   	 retrun;
	       }  
	 
		document.UserDataForm.action.value = 'HideUser';
		document.UserDataForm.submit();
	}


	// Функция получения конфигурации
	function GetDeviceId(deviceid)
	{ 
		document.UserDevicesForm.DeviceId.value = deviceid;
		document.UserDevicesForm.action.value = "GetDeviceId";
		document.UserDevicesForm.submit();
	}

	// Функция добавления устройства
	function AddDevice()
	{ 
		document.UserDevicesForm.action.value = "AddDevice";
		document.UserDevicesForm.submit();
	}

	// Функция отправки сообщения
	function SendMessage()
	{ 
		document.UserSendMessageForm.action.value = "SendMessage";
		document.UserSendMessageForm.submit();
	}


	// Функция добавления впечатдения
	function AddLink()
	{ 
		document.UserLinksForm.action.value = "AddLink";
		document.UserLinksForm.submit();
	}

	// Функция добавления впечатдения
	function DelLink(linkid)
	{ 
                document.UserLinksForm.action.value = "DelLink";
		document.UserLinksForm.UserLinkId.value = linkid;
		document.UserLinksForm.submit();
	}

</script>
<!-- Конец вывода javascrpit -->

<?php

         // выводим форму с данными пользователя
	 
	 print('<form  name = "UserDataForm"  action = "'.$MyPHPScript.'" method = "post" onSubmit = "'.$OnSubmitFunction.'">'."\r\n");
         print('<input type = "hidden" name = "UserId" value = "'.$pUserId.'">'."\r\n");
         print('<input type = "hidden" name = "action" value = "">'."\r\n");

	 if ($AllowEdit == 1) 
	 {
          print('<div style = "margin-top: 10px; margin-bottom: 10px; font-size: 80%; text-align: left">Всплывающие подсказки появляются при наведении курсора мыши:</div>'."\r\n");
	 } 

         print('<table  class = "menu" border = "0" cellpadding = "0" cellspacing = "0" width = "300">'."\r\n");
	 $TabIndex = 0;
	 
         // Если не разрешена правка - не показываем адрес почты
         if ($AllowEdit == 1) 
	 {
	  print('<tr><td class = "input"><input type="text" autocomplete = "off" name="UserEmail" size="50" value="'.$UserEmail.'" tabindex = "'.(++$TabIndex).'"  '.$DisabledText.' '
		.($viewmode <> 'Add' ? '' : CMmbUI::placeholder($UserEmail))
		.' title = "E-mail - Используется для идентификации пользователя"></td></tr>'."\r\n");
         }

         print('<tr><td class = "input"><input type="text" autocomplete = "off" name="UserName" size="50" value="'.$UserName.'" tabindex = "'.(++$TabIndex).'"   '.$DisabledText.' '
		.($viewmode <> 'Add' ? '' : CMmbUI::placeholder($UserName))
	        .' title = "ФИО - Так будет выглядеть информация о пользователе в протоколах и на сайте"></td></tr>'."\r\n");

         print('<tr><td class = "input"><input type="text" autocomplete = "off" name="UserBirthYear" maxlength = "4" size="11" value="'.$UserBirthYear.'" tabindex = "'.(++$TabIndex).'" '.$DisabledText.' '
		.($viewmode <> 'Add' ? '' : CMmbUI::placeholder($UserBirthYear))
	        .' title = "Год рождения"></td></tr>'."\r\n");

         // Пустой $UserCity  выше  заменяется на подсказку
         print('<tr><td class = "input"><input type="text" autocomplete = "off" name="UserCity" size="50" value="'.$UserCity.'" tabindex = "'.(++$TabIndex).'"   '.$DisabledText.' '
	        .($UserCity <> $UserCityPlaceHolder ? '' : CMmbUI::placeholder($UserCityPlaceHolder))
	        .' title = "Город"></td></tr>'."\r\n");

	 if ($AllowEdit == 1)
        {
         print('<tr><td class = "input"><input type="text" autocomplete = "off" name="UserPhone" size="20" value="'.$UserPhone.'" tabindex = "'.(++$TabIndex).'"   '.$DisabledText.' '
	        .($UserPhone <> $UserPhonePlaceHolder ? '' : CMmbUI::placeholder($UserPhonePlaceHolder))
	        .' title = "Телефон"></td></tr>'."\r\n");
        }
//                 '.( $UserCity <> $UserCityPlaceHolder ? '' : 'onclick = "javascript: this.value=\'\';" onblur = "javascript: this.value=\''.$UserCityPlaceHolder.'\';"').'

         print('<tr><td class = "input"><input type="checkbox"  autocomplete = "off" name="UserProhibitAdd" '.(($UserProhibitAdd == 1) ? 'checked="checked"' : '').' tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
	        title = "Даже зная адрес e-mail, другой пользователь не сможет сделать Вас участником своей команды - только Вы сами или модератор ММБ" /> Нельзя включать в команду другим пользователям</td></tr>'."\r\n");

          // 03/07/2014 
         print('<tr><td class = "input"><input type="checkbox"  autocomplete = "off" name="UserNoShow" '.(($UserNoShow == 1) ? 'checked="checked"' : '').' tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
	        title = "Вместо ФИО будет выводится текст: \''.$Anonimus.'\' Исключения: 1) Вы сами, модератор и администратор увидят в карточке пользователя ФИО. 2) При запросе на объединение с другим пользователем, инициатором которого являетесь Вы, ему будет открываться Ваше ФИО в журнале объединений и письме, чтобы он знал, кто пытается его присоединить к себе." /> Не показывать моё ФИО в результатах, рейтингах и т.п.</td></tr>'."\r\n");


         print('<tr><td class = "input"><input type="checkbox"  autocomplete = "off" name="UserAllowChangeInfo" '.(($UserAllowChangeInfo == 1) ? 'checked="checked"' : '').' tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
	        title = "На Ваш email будет отправляться письмо, когда вносятся изменения в карточку пользователя или в данные команды, участником которой Вы являетесь." />Получать информацию при изменении данных пользователя или команды. <br/><i>Не рекомендуется снимать этот флаг</i></td></tr>'."\r\n");

         print('<tr><td class = "input"><input type="checkbox"  autocomplete = "off" name="UserAllowOrgMessages" '.(($UserAllowOrgMessages == 1) ? 'checked="checked"' : '').' tabindex = "'.(++$TabIndex).'" '.$DisabledText.'
	        title = "На Ваш email будет отправляться письмо, когда открывается регистрация или когда организаторы осуществляют рассылку важной информации." />Получать информацию об открытии информации, о публикации результатов и т.п. <br/><i>Если флаг не установлен, то информационное письмо может прийти только в случае экстренной рассылки.</i></td></tr>'."\r\n");


	 if (($AllowEdit == 1) and  ($viewmode <> 'Add'))
        {

	  print('<tr><td class = "input" style =  "padding-top: 15px;">Новый пароль: <input type="password" autocomplete = "off" name="UserNewPassword" size="30" value="" tabindex = "'.(++$TabIndex).'"   '.$DisabledText.'
                  title = "Новый пароль"></td></tr>'."\r\n");

	  print('<tr><td class = "input">Подтверждение: <input type="password" autocomplete = "off" name="UserConfirmNewPassword" size="30" value="" tabindex = "'.(++$TabIndex).'"   '.$DisabledText.'
                  title = "Подтверждение нового пароля"></td></tr>'."\r\n");

        }


         // Если не разрешена права - не показываем кнопки
	 if ($AllowEdit == 1) 
	 {
	  print('<tr><td class = "input"  style =  "padding-top: 10px;">'."\r\n");
	  print('<input type="button" onClick = "javascript: if (ValidateUserDataForm()) submit();"  name="RegisterButton" value="'.$SaveButtonText.'" tabindex = "7">'."\r\n");

           // Если регистрация нового пользователя - не нужны кнопки "Отмена" и "Сменить пароль"
          if ($viewmode <> 'Add')
	  {
            print('<input type="button" onClick = "javascript: Cancel();"  name="CancelButton" value="Отмена" tabindex = "8" title = "Заново считывает данные из базы">'."\r\n");		

	    // 15,01,2012 убрал проверку
	    // М.б. проверка лишняя и нужно разрешить и администратору высылать запрос о смене пароля
	    //if ($UserId > 0 and $UserId == $NowUserId)
	    //{
             print('<input type="button" onClick = "javascript: if (confirm(\'Вы уверены, что хотите выслать на адрес '.trim($UserEmail).' новый пароль для '.trim($UserName).' будет создан новый пароль и ? \')) { NewPassword(); }"  name="ChangePasswordButton" value="Создать и выслать новый пароль" tabindex = "9">'."\r\n");		
	    //}
          }

          print('</td></tr>'."\r\n"); 
         }
         // Конец вывода кнопок

         $ModeratorUnionString = '';

         // для Администратора добавляем кнопку "Сделать модератором" в правке пользователя
	 if ($Administrator and $viewmode <> 'Add') 
	 {
	   $ModeratorUnionString .= '<input type="button" onClick = "javascript: if (confirm(\'Вы уверены, что хотите сделать этого пользователя модератором текущего марш-броска? \')) { MakeModerator(); }"  name="ModeratorButton" value="'.$ModeratorButtonText.'" tabindex = "'.(++$TabIndex).'">';
         }
	 

	 if (CanRequestUserUnion($Administrator, $UserId, $pUserId)) {

	   $ModeratorUnionString .= '<input type="button" onClick = "javascript: if (confirm(\'Вы уверены, что хотите оставить запрос на слияние этого пользователя с Вашей учетной записью? \')) { MakeUnionRequest(); }"  name="UnionButton" value="'.$UnionButtonText.'" tabindex = "'.(++$TabIndex).'">';
	 
	 }

	 
	 if (trim($ModeratorUnionString) <> '') {
	    print('<tr><td class = "input"  style =  "padding-top: 10px;">'.$ModeratorUnionString.'</td></tr>'."\r\n");
	 }
	 // Конец проверки, что есть кнопка сделать модератором или запросить слияние

         print('</tr>'."\r\n"); 

	 if ($AllowEdit == 1) 
	 {
	  print('<tr><td class = "input"  style =  "padding-top: 10px;">Ключ пользователя: '.$pUserId.'</td></tr>'."\r\n");
	 }
         
	 
	 // ============ Кнопка удаления всей команды для тех, кто имеет право
 	 if ($Administrator  and $viewmode <> 'Add') 
	 {
	  print('<tr><td class = "input"  style =  "padding-top: 10px;">'."\r\n");
	           print('<input type="button" style="padding-left: 30px;padding-right: 30px;margin-right:15px;" onClick="javascript: if (confirm(\'Вы уверены, что хотите удалить пользователя: '.trim($UserName).'? \')) {HideUser();}" name="HideUserButton" value="Удалить пользователя" tabindex="'.(++$TabIndex).'">'."\n");

           print(' Подтверждение удаления <input type="checkbox" name="UserHideConfirm" tabindex = "'.(++$TabIndex).'" disabled 
	        title = "Защита от случайного удаления" /> '."\r\n");

	  
	  print('</td></tr>'."\r\n");
	 }

	 
	 print('</table></form>'."\r\n"); 
	 // Конец вывода формы с данными пользователя

	 

          // Выводим спсиок команд, в которых участвовал данный пользователь 
          print('<div style = "margin-top: 20px; margin-bottom: 10px; text-align: left">Участвовал:</div>'."\r\n");
          print('<form  name = "UserTeamsForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
          print('<input type = "hidden" name = "action" value = "">'."\r\n");
	  print('<input type = "hidden" name = "RaidId" value = "0">'."\n");
	  print('<input type = "hidden" name = "TeamId" value = "0">'."\n");

		
                 
		$sql = "select tu.teamuser_id, t.team_name, t.team_id, 
		               d.distance_name, r.raid_name, t.team_num, 
			       r.raid_id, lp.levelpoint_name,
			       lp.levelpoint_id, COALESCE(tu.teamuser_rank, 0.00000) as teamuser_rank  
		        from  TeamUsers tu
			      inner join  Teams t
			      on tu.team_id = t.team_id
			      inner join  Distances d
			      on t.distance_id = d.distance_id
			      inner join  Raids r
			      on d.raid_id = r.raid_id
			      left outer join TeamLevelDismiss  tld
			      on tu.teamuser_id = tld.teamuser_id
			      left outer join LevelPoints  lp
			      on tld.levelpoint_id = lp.levelpoint_id
			where tu.teamuser_hide = 0 and tu.user_id = $pUserId
			order by r.raid_id desc "; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{

			$TeamPlace = GetTeamPlace($Row['team_id']);
			$LevelPointId = $Row['levelpoint_id'];
			$TeamPlaceResult = "";
			// Есть место команды и нет схода участника
			if ($TeamPlace > 0 and $LevelPointId == 0) $TeamPlaceResult = "место <b>$TeamPlace</b>";

               

			$TeamUserOff = "";
			// Есть место команды, но сход участника
			if ($TeamPlace > 0 and $LevelPointId > 0) $TeamUserOff = "не явка в точку <b>{$Row['levelpoint_name']}</b>";

			$comma = ($TeamPlace > 0 and $LevelPointId >= 0) ? ',' : '';


		  print('<div class="team_res"><span><a href="?TeamId='.$Row['team_id'].'&RaidId='.$Row['raid_id'].'"  title = "Переход к карточке команды">'.CMmbUI::toHtml($Row['team_name'])."</a>
		         N {$Row['team_num']}$comma</span> $TeamPlaceResult$TeamUserOff ({$Row['teamuser_rank']}), дистанция: {$Row['distance_name']}, ммб: {$Row['raid_name']}</div>\r\n");
		}

                mysql_free_result($Result);
	        print("</form>\r\n");

	  if ($viewmode <> 'Add' and $AllowEdit == 1)
	  {
		// Выводим спсиок устройств, которые относятся к данному пользователю 
	        print('<div style = "margin-top: 20px; margin-bottom: 10px; text-align: left">Устройства:</div>'."\r\n");
		print('<form  name = "UserDevicesForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
		print('<input type = "hidden" name = "action" value = "">'."\r\n");
		print('<input type = "hidden" name = "UserId" value = "'.$pUserId.'">'."\n");
		print('<input type = "hidden" name = "DeviceId" value = "0">'."\n");

		
                 
		$sql = "select d.device_id, d.device_name
		        from  Devices d
			where d.user_id = $pUserId
			order by device_id desc "; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{
		  print('<div class="team_res">'.CMmbUI::toHtml($Row['device_name']).' <a href = "javascript:GetDeviceId('.$Row['device_id'].');"
		          title = "Получить файл конфигурации">Конфигурация</a></div>'."\r\n");
		}

                mysql_free_result($Result);

                $TabIndex = 1;
	        $DisabledText = '';
                $NewDeviceName = 'Название нового устройства';
		print('<div class="team_res"><input type="text" name="NewDeviceName" size="50" value="'.$NewDeviceName.'" tabindex = "'.(++$TabIndex).'"  '.$DisabledText.' '
                . CMmbUI::placeholder($NewDeviceName) . ' title = "Название нового устройства">'."\r\n");
    	        print('<input type="button" onClick = "javascript: AddDevice();"  name="AddDeviceButton" value="Добавить" tabindex = "'.(++$TabIndex).'">'."\r\n");
                   
	        print('</div></form>'."\r\n");

	   }
	   // Конец проверки на режим правки
	   
	  
	  // 23/04/2014 Отправка сообщения через почту 
	  if ($viewmode <> 'Add' and !empty($UserId))
	  {
		// Выводим спсиок устройств, которые относятся к данному пользователю 
	        print('<div style = "margin-top: 20px; margin-bottom: 10px; text-align: left">Cообщение для пользователя '.$UserName.':</div>'."\r\n");
		print('<form  name = "UserSendMessageForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
		print('<input type = "hidden" name = "action" value = "">'."\r\n");
		print('<input type = "hidden" name = "UserId" value = "'.$pUserId.'">'."\n");


                $TabIndex = 1;
	        $DisabledText = '';
		print('<div class="team_res"><textarea name="MessageText"  rows="4" cols="50" tabindex = "'.(++$TabIndex).'"  '.$DisabledText.'
	        title = "Текст сообщения">Текст сообщения</textarea></div>'."\r\n");
    	        print('</br><input type="button" onClick = "javascript: SendMessage();"  name="SendMessageButton" value="Отправить" tabindex = "'.(++$TabIndex).'">'."\r\n");
                   
	        print('</form>'."\r\n");

	   }
	   // Конец проверки на режим правки и авторизованного пользоватлея


          // 04.07.2014  Блок ссылок на впечатления.
	  if ($viewmode <> 'Add' and  UACanLinkEdit($pUserId, $RaidId, $UserId) == 1)
	  {
		// Выводим спсиок впечатлений, которые относятся к данному пользователю 
	        print('<div style = "margin-top: 20px; margin-bottom: 10px; text-align: left">Впечатления:</div>'."\r\n");
		print('<form  name = "UserLinksForm"  action = "'.$MyPHPScript.'" method = "post">'."\r\n");
		print('<input type = "hidden" name = "action" value = "">'."\r\n");
		print('<input type = "hidden" name = "UserId" value = "'.$pUserId.'">'."\n");
		print('<input type = "hidden" name = "RaidId" value = "'.$RaidId.'">'."\n");
		print('<input type = "hidden" name = "UserLinkId" value = "0">'."\n");

		
                 
		$sql = "select ul.userlink_id, ul.userlink_name, lt.linktype_name,
		               ul.userlink_url, r.raid_name, lt.linktype_textonly 
		        from  UserLinks ul
			      inner join LinkTypes lt  on ul.linktype_id = lt.linktype_id
			      inner join Raids r on ul.raid_id = r.raid_id 
			where ul.userlink_hide = 0 and ul.user_id = $pUserId
			order by userlink_id  "; 
                //echo 'sql '.$sql;
		$Result = MySqlQuery($sql);

		while ($Row = mysql_fetch_assoc($Result))
		{

                  $Label =  (empty($Row['userlink_name'])) ?  $Row['userlink_url'] : CMmbUI::toHtml($Row['userlink_name']);
		  print('<div class="team_res">'.$Row['raid_name'].' '.$Row['linktype_name'].' <a href = "'.CMmbUI::toHtml($Row['userlink_url']).'"
		          title = "'.CMmbUI::toHtml($Row['userlink_name']).'">'.$Label.'</a>'."\r\n");
                  print('<input type="button" style = "margin-left: 20px;" onClick = "javascript: if (confirm(\'Вы уверены, что хотите удалить впечатление ? \')) {DelLink('.$Row['userlink_id'].');}"  name="DelLinkButton" value="Удалить" tabindex = "'.(++$TabIndex).'">'."\r\n");
                  print('</div>'."\r\n");
			  
		}

                mysql_free_result($Result);

                $TabIndex = 1;
	        $DisabledText = '';
                $NewLinkName = 'Название (можно не заполнять)';
                $NewLinkUrl = 'Адрес ссылки';

		print('<div align = "left" style = "padding-top: 5px;">'."\r\n");

		// Показываем выпадающий список ММБ
		print('<select name="LinkRaidId"  tabindex="'.(++$TabIndex).'">'."\n");
		
		$RaidCondition = '';
		if (CSql::userModerator($UserId, $RaidId) == 1 and $UserId <> $pUserId)
		{
			$RaidCondition = "where raid_id = $RaidId";
		}
		
		$sql = "select raid_id, raid_name from Raids $RaidCondition order by raid_id  desc";
		$Result = MySqlQuery($sql);
		while ($Row = mysql_fetch_assoc($Result))
		{
			$raidselected =  ($Row['raid_id'] == $RaidId)  ? ' selected ' : '';
			print('<option value="'.$Row['raid_id'].'" '.$raidselected.' >'.$Row['raid_name']."</option>\n");
		}
		mysql_free_result($Result);
		print('</select>'."\n");

		// Показываем выпадающий список типов ссылок
		print('<select name="LinkTypeId" class="leftmargin" tabindex="'.(++$TabIndex).'">'."\n");
		$sql = "select linktype_id, linktype_name, linktype_textonly from LinkTypes where linktype_hide = 0  order by linktype_order asc ";
		$Result = MySqlQuery($sql);
		while ($Row = mysql_fetch_assoc($Result))
		{
			$linktypeselected = '';
			$LinkNameDisabled =  (empty($Row['linktype_textonly']) ? 'false' : 'true');
			print('<option value="'.$Row['linktype_id'].'" '.$linktypeselected.'  onclick = "javascript:document.UserLinksForm.NewLinkName.disabled='.$LinkNameDisabled.';">'.$Row['linktype_name']."</option>\n");
		}
		mysql_free_result($Result);
		print('</select>'."\n");

		print('<input type="text" name="NewLinkName" size="30" value="'.$NewLinkName.'" tabindex = "'.(++$TabIndex).'"  '.$DisabledText.' '
		. CMmbUI::placeholder($NewLinkName) . ' title = "Название нового впечатления">'."\r\n");
	        print("</div>\r\n");

		print('<div align = "left" style = "padding-top: 5px;">'."\r\n");

		print('<input type="text" name="NewLinkUrl" size="50" value="'.$NewLinkUrl.'" tabindex = "'.(++$TabIndex).'"  '.$DisabledText.' '
		. CMmbUI::placeholder($NewLinkUrl) . ' title = "Адрес ссылки на впечатление">'."\r\n");
    	        print('<input type="button" onClick = "javascript: AddLink();"  name="AddLinkButton" value="Добавить" tabindex = "'.(++$TabIndex).'">'."\r\n");
                   
	        print("</div></form>\r\n");

	   }
	   // Конец блока ссылок на впечатления
	   
	   
?>



