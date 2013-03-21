function populate(name, email, gender, address, zip, phone, age, income)
{
  document.edituser.name.value    = name;
  document.edituser.email.value   = email;
  if(gender === 'M')
  {
    document.getElementById("gender1").checked = true;
    document.getElementById("gender2").checked = false;
  }
  else
  {
    document.getElementById("gender1").checked = false;
    document.getElementById("gender2").checked = true;
  }
  document.edituser.address.value = address;
  document.edituser.zip.value     = zip;
  document.edituser.phone.value   = phone;
  document.edituser.age.value     = age;
  document.edituser.income.value  = income;
}
