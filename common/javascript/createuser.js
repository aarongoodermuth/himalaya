function Registered_User_selected()
{
  // disable all supplier form elements
  document.createuser.company.disabled = true;
  document.createuser.contact.disabled = true;

  // enable all registered user elements
  document.createuser.name.disabled = false;
  document.createuser.email.disabled = false;
  document.getElementById("gender1").disabled = false;
  document.getElementById("gender2").disabled = false;
  document.createuser.address.disabled = false;
  document.createuser.zip.disabled = false;
  document.createuser.phone.disabled = false;
  document.createuser.age.disabled = false;
  document.createuser.income.disabled = false;
}

function Supplier_selected()
{

  // enable all supplier form elements
  document.createuser.company.disabled = false;
  document.createuser.contact.disabled = false;

  // disable all registered user elements
  document.createuser.name.disabled = true;
  document.createuser.email.disabled = true;
  document.getElementById("gender1").disabled = true;
  document.getElementById("gender2").disabled = true;
  document.createuser.address.disabled = true;
  document.createuser.zip.disabled = true;
  document.createuser.phone.disabled = true;
  document.createuser.age.disabled = true;
  document.createuser.income.disabled = true;
}

function disable_all()
{
  // disable all supplier form elements
  document.createuser.company.disabled = true;
  document.createuser.contact.disabled = true;

  // disable all registered user elements
  document.createuser.name.disabled = true;
  document.createuser.email.disabled = true;
  document.getElementById("gender1").disabled = true;
  document.getElementById("gender2").disabled = true;
  document.createuser.address.disabled = true;
  document.createuser.zip.disabled = true;
  document.createuser.phone.disabled = true;
  document.createuser.age.disabled = true;
  document.createuser.income.disabled = true;
}
