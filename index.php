<?php
session_start();
require_once 'submiit.php';
require './abstractmodel.php';
require_once 'employees.php';

if(isset($_POST['submit'])) {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $tax = filter_input(INPUT_POST, 'tax', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $salary=$salary-(($tax*$salary)/100);
   // $employee = new Employees($name,$age,$address,$tax,$salary);
 
    if (isset($_GET['action'])&& $_GET['action']== 'edit' && isset($_GET['id']))
    {
          $id= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

         if($id > 0)
             {
                 $user= Employees::getByID($id); 
                 $user->setName($name);
                 $user->setAge($age);
                 $user->setAddress($address);
                 $user->setSalary($salary);
                 $user->setTax($tax);
                 }
    }
    else{
          $user= new Employees($name, $age, $address, $tax, $salary); 
        
   }
    if ($user->save()===true ) {
        $_SESSION['mes'] = 'the information of employee saved succefully ';
        header('location:  http://localhost/pdoProject');
        session_write_close();
        exit;
    } else {
        $error = true;
        $_SESSION['mes'] = 'there is some thing wrong';
    }
}
if(isset($_GET['action'])&& $_GET['action']== 'edit' && isset($_GET['id']))
{
    $id= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if($id > 0)
    {
        $user= Employees::getByID($id); 
    }
}
if (isset($_GET['action'])&& $_GET['action']== 'delete' && isset($_GET['id']))
{
       $id= filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);  
    if($id > 0)
    {
       $user= Employees::getByID($id);
    if($user->delete() === true)
    {
        $_SESSION['mes'] = 'the information of employee deleted succefully ';
        header('location:  http://localhost/pdoProject');
        session_write_close();
        exit;

    }
    }
}
    $result= Employees::getAll();
 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Title</title>

    <link type="text/css" href="font-awesome.css" rel="stylesheet"/>
    <link type="text/css" href="main2.css" rel="stylesheet"/>
</head>
<body>
<div class="wrapper">
<div class="empform">
<form class="appform"  method="post" enctype="application/x-www-form-urlencoded">

    <fieldset>
        <legend>Personal info</legend>
        <?php if(isset($_SESSION['mes'])) {?>
        <p class=" message <?= isset($error)? 'error' : '' ?>">
            <?= $_SESSION['mes' ] ?>
        </p>
        <?php
        unset($_SESSION['mes']);
        }?>
        <table>
            <tr>
              <td>  <label for="username"> Employee Name :  </label></td>
            </tr>
            <tr>
                <td><input id="username" required type="text" name="name"  placeholder="Write Your Name Here" tabindex="1" value="<?= isset($user)? $user->name : '' ?>"></td>
            </tr>
            <tr>
                <td>  <label for="Age">Employee Age :</label></td>
            </tr> 
            <tr>
                <td><input id="Age" type="number" name="age" placeholder="Write Your Age Here" tabindex="2"  required value="<?= isset($user)? $user->age : '' ?>" ></td>
            </tr>
            <tr>
                <td>  <label for="address" > address :</label></td>
            </tr>
            <tr>
                <td><input id="address" type="text" name="address" placeholder="Write Your Address Here" tabindex="3"  required value=" <?= isset($user)? $user->address :'' ?>"  ></td>
            </tr>

            <tr>
                <td>  <label for="salary"> Employee Salary :  </label></td>
            </tr>
            <tr>
                <td><input required id="salary" type="number" name="salary" placeholder="Write Your Salary Here" step="0.01"   tabindex="4" min="1500" max="9000" value="<?= isset($user)? $user->salary : '' ?>"></td>
            </tr>
            <tr>
                <td>  <label for="tax"> Employee Tax :  </label></td>
            </tr>
            <tr>
                <td><input required type="number" id="tax" placeholder="Write Tax Here" name="tax" step="0.01"   tabindex="4" min="1" max="5" value="<?= isset($user)? $user->tax : '' ?>"></td>
            </tr>
        </table>
    </fieldset>
    <table>
        <tr>
            <td><input class="submit" type="submit" name="submit" value="submit"></td>
        </tr>
    </table>
</form>
</div>
    <div class="employees">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Address</th>
                <th>salary</th>
                <th>Tax (%)</th>
                <th>edit</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if(false!==$result)
                {
                    foreach ($result as $employee)
                    {
                        ?>
                  <tr>
                <td><?= $employee->name ?></td>
                <td><?= $employee->age ?></td>
                <td><?= $employee->address ?></td>
                <td><?= round($employee->salary) ?></td>
                <td><?= $employee->tax ?></td>
                      <td>
                          <a href="/pdoProject/?action=edit&id=<?= $employee->id ?>"><i class="fa fa-edit"></i></a>
                          <a href="/pdoProject/?action=delete&id=<?= $employee->id ?>" onclick="if(!confirm('Do yo want to delete')) return false;" ><i class="fa fa-times"></i></a>
                      </td>
                      </tr>
<?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>