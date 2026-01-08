<?php 
/** @var \Unibostu\Dto\FacultyDto $faculty */
/*tempory view , da migliorare con select dinamica delle facoltà dal DB*/
$this->extend('main-layout', ['title' => 'Register Unibostu']); 
?>

    <form class="register" id="register-form" method="post">
        <fieldset>
        <legend>Register to Unibostu</legend>
                <ul>
                    <li>
                    <label for="fname">First name:</label>
                    <input type="text" name="fname" id="fname" placeholder="Insert your name" required/>
                    </li>
                    <li>
                    <label for="lname">Last name:</label>
                    <input type="text" name="lname" id="lname" placeholder="Insert your surname" required/>
                    </li>
                    <li>
                    <label for="email" > Unibo E-mail:</label>
                    <input type="email" name="email" id="email" placeholder="Insert your E-mail" required/>
                    </li>
                    <li>
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="Username of your choice" required/>
                    </li>
                    <li>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Insert your password" minlength="8" required/>
                    </li>
                    <li><!--confirmPassword da fare o no? boh , + nascondi password da fare o no ? boh--> 
                    <label for="confirmPassword" id="labelConfirmPassword">Confirm password:</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password" minlength="8" required/>
                    </li>
                    <li>
                    <label for="faculty" >Faculty:</label>
                    <select id="faculty" name="faculty" required>
                    <?/* if (!empty($faculties)): ?>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?= htmlspecialchars($faculty->facultyId) ?>">
                            <?= htmlspecialchars($faculty->facultyName) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; */?>
                    <option value="cs">Computer Science</option> <!--da sostituire con le facoltà del DB-->
                    </select>
                    </li>
                    <li>
                        <button type="submit" id="register-form_submit">Register</button>
                    </li>
         <form class="register" id="register-form" method="post">
            

