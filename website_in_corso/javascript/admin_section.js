const content = document.getElementById("admin-content");
const buttons = document.querySelectorAll(".admin-menu button");

buttons.forEach(btn => {
  btn.addEventListener("click", () => {
    loadView(btn.id);
  });
});

function getAddFacultyForm() {
  return `
    <h3>Add Faculty</h3>

    <form id="form-add-faculty" class="admin-form">
      <label>
        Faculty name
        <input type="text" name="faculty_name" required>
      </label>

      <label>
        Description
        <textarea name="faculty_description"></textarea>
      </label>

      <button type="submit">Create faculty</button>
    </form>
  `;
}

function getAddTagForm() {
  return `
    <h3>Add Tag</h3>

    <form id="form-add-tag" class="admin-form">

      <label>
        Faculty
        <select name="faculty_id" id="select-faculty-tag" required>
          <option value="">Select faculty</option>
          <option value="1">Informatica</option>
          <option value="2">Ingegneria</option>
        </select>
      </label>

      <label>
        Course
        <select name="course_id" id="select-course-tag" required disabled>
          <option value="">Select course</option>
        </select>
      </label>

      <label>
        Tag name
        <input type="text" name="tag_name" required>
      </label>

      <button type="submit">Create tag</button>
    </form>
  `;
}

function getAddCourseForm() {
  return `
    <h3>Add Course</h3>

    <form id="form-add-course" class="admin-form">

      <label>
        Faculty
        <select name="faculty_id" required>
          <option value="">Select faculty</option>
          <option value="1">Informatica</option>
          <option value="2">Ingegneria</option>
        </select>
      </label>

      <label>
        Course name
        <input type="text" name="course_name" required/>
      </label>

      <button type="submit">Create course</button>
    </form>
  `;
}

function getDeletePostsTable() {

  // MOCK DATA
  const posts = [
    { id: 1, title: "Post A", dislikes: 12 },
    { id: 2, title: "Post B", dislikes: 45 },
    { id: 3, title: "Post C", dislikes: 7 }
  ];

  posts.sort((a, b) => b.dislikes - a.dislikes);

  let rows = posts.map(p => `
    <tr>
      <td>${p.title}</td>
      <td>${p.dislikes}</td>
      <td><button data-delete-post="${p.id}">Delete</button></td>
    </tr>
  `).join("");

  return `
    <h3>Delete Posts</h3>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Dislikes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        ${rows}
      </tbody>
    </table>
  `;
}

function getChangeFacultyForm() {
  return `
    <h3>Change Faculty</h3>

    <form id="form-change-faculty" class="admin-form">

      <label>
        Faculty
        <select name="faculty_id" required>
          <option value="">Select faculty</option>
          <option value="1">Informatica</option>
          <option value="2">Ingegneria</option>
        </select>
      </label>

      <label>
        New name
        <input type="text" name="new_name">
      </label>

      <label>
        New description
        <textarea name="new_description"></textarea>
      </label>

      <button type="submit">Update faculty</button>
    </form>
  `;
}

content.addEventListener("submit", e => {

  e.preventDefault();

  if (e.target.id === "form-add-faculty") {
    console.log("ADD FACULTY", Object.fromEntries(new FormData(e.target)));
  }

  if (e.target.id === "form-add-course") {
    console.log("ADD COURSE", Object.fromEntries(new FormData(e.target)));
  }

  if (e.target.id === "form-add-tag") {
    console.log("ADD TAG", Object.fromEntries(new FormData(e.target)));
  }

  if (e.target.id === "form-change-faculty") {
    console.log("CHANGE FACULTY", Object.fromEntries(new FormData(e.target)));
  }

});

content.addEventListener("change", e => {
  if (e.target.id === "select-faculty-tag") {

    const facultyId = e.target.value;
    const courseSelect = document.getElementById("select-course-tag");

    courseSelect.innerHTML = `<option value="">Select course</option>`;
    courseSelect.disabled = true;

    if (!facultyId) return;

    const courses = {
      1: [
        { id: 10, name: "Programmazione" },
        { id: 11, name: "Basi di dati" }
      ],
      2: [
        { id: 20, name: "Fisica" },
        { id: 21, name: "Analisi" }
      ]
    };

    courses[facultyId].forEach(c => {
      courseSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
    });

    courseSelect.disabled = false;
  }
});

function loadView(action) {
  switch(action) {

    case "add-faculty":
      content.innerHTML = getAddFacultyForm();
      break;

    case "add-course":
      content.innerHTML = getAddCourseForm();
      break;

    case "add-tag":
      content.innerHTML = getAddTagForm();
      break;

    case "delete-posts":
      content.innerHTML = getDeletePostsTable();
      break;

    case "change-faculty":
      content.innerHTML = getChangeFacultyForm();
      break;

    default:
      content.innerHTML = "<p>Seleziona un'azione</p>";
  }
}
