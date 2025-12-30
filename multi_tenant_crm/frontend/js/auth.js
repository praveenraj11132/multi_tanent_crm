function getToken() {
  return localStorage.getItem('token');
}

function logout() {
  localStorage.clear();
  location.href = 'login.html';
}
