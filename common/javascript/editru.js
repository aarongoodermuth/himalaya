function populate(name, email, gender, address, zip, phone, age, income)
{
  document.createuser.name.value    = name;
  document.createuser.email.value   = email;
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
  document.createuser.address.value = address;
  document.createuser.zip.value     = zip;
  document.createuser.phone.value   = phone;
  document.createuser.age.value     = age;
  document.createuser.income.value  = income;
}
