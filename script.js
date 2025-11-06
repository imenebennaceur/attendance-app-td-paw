/* ===== Attendance Logic ===== */
function evaluateRow(tr){
  const sessionChecks = Array.from(tr.querySelectorAll('td:nth-child(n+5):nth-child(-n+10) input'));
  const partChecks    = Array.from(tr.querySelectorAll('td:nth-child(n+11):nth-child(-n+16) input'));
  const presents = sessionChecks.filter(c=>c.checked).length;
  const absences = sessionChecks.length - presents;
  const parts    = partChecks.filter(c=>c.checked).length;

  tr.querySelector('.absences').textContent = absences;
  tr.querySelector('.participations').textContent = parts;

  tr.classList.remove('abs-low','abs-mid','abs-high');
  if(absences<3) tr.classList.add('abs-low');
  else if(absences<=4) tr.classList.add('abs-mid');
  else tr.classList.add('abs-high');

  const msgCell = tr.querySelector('.message');
  let msg="";
  if(absences>=5) msg="Excluded – too many absences – You need to participate more";
  else if(absences>=3)
    msg = parts>=3 ? "Warning – attendance low – Good participation"
                   : "Warning – attendance low – You need to participate more";
  else
    msg = parts>=4 ? "Good attendance – Excellent participation"
                   : "Good attendance – Keep participating";
  msgCell.textContent = msg;
}

function evaluateAll(){
  document.querySelectorAll('#attendanceTable tbody tr').forEach(evaluateRow);
}
document.addEventListener('change',e=>{
  if(e.target.matches('#attendanceTable input[type="checkbox"]')){
    evaluateRow(e.target.closest('tr'));
  }
});
evaluateAll();

/* ===== Form Validation ===== */
const form = document.getElementById('studentForm');
const fields={
  studentId:{el:studentId,wrap:f-studentId,test:v=>/^\d+$/.test(v.trim())},
  lastName:{el:lastName,wrap:f-lastName,test:v=>/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/.test(v.trim())},
  firstName:{el:firstName,wrap:f-firstName,test:v=>/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/.test(v.trim())},
  email:{el:email,wrap:f-email,test:v=>/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim())}
};
function showError(wrap,ok){
  wrap.classList.toggle('invalid',!ok);
  const msg=wrap.querySelector('.error-msg');
  if(msg) msg.style.display=ok?'none':'block';
}
function validateField(f){
  const ok=f.test(f.el.value);
  showError(f.wrap,ok);
  return ok;
}
Object.values(fields).forEach(f=>{
  f.el.addEventListener('input',()=>validateField(f));
  f.el.addEventListener('blur',()=>validateField(f));
});
form.addEventListener('submit',e=>{
  const results=Object.values(fields).map(validateField);
  if(results.some(ok=>!ok)){ e.preventDefault(); return; }
  e.preventDefault();
  const tbody=document.querySelector('#attendanceTable tbody');
  const tr=document.createElement('tr');
  tr.innerHTML=`
    <td>${fields.studentId.el.value.trim()}</td>
    <td>${fields.lastName.el.value.trim()}</td>
    <td>${fields.firstName.el.value.trim()}</td>
    <td>AWP</td>
    ${Array.from({length:6}).map(()=>'<td><input type="checkbox"></td>').join('')}
    ${Array.from({length:6}).map(()=>'<td><input type="checkbox"></td>').join('')}
    <td class="absences">0</td>
    <td class="participations">0</td>
    <td class="message"></td>`;
  tbody.appendChild(tr);
  evaluateRow(tr);
  form.reset();
  Object.values(fields).forEach(f=>showError(f.wrap,true));
  alert('Student added locally (demo). Replace with real submit later.');
});
