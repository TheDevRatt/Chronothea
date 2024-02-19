<!DOCTYPE html>
<html lang="en">
<!--The head tag, contains the meta information along with the title describing the page.-->

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Slot Sign-Up</title>
  <script defer src="https://kit.fontawesome.com/c5029c768a.js" crossorigin="anonymous"></script>
  <link href="./css/main.css" rel="stylesheet">
</head>

<!--The body tag, contains the body of the html file-->

<body>
  <!--The nav tag, contains a header with the name of the "comapny" and all the links to navigate the pages so far.-->
  <nav>
    <div class="company-header">
      <h1>Chronothea</h1>
      <p><small>Your one stop shop for all your school sign-up's</small></p>
    </div>
    <ul>
      <li><a href="./index.html">Your Stuff</a></li>
      <li><a href="./addsheet.html">Create Event</a></li>
      <li><a href="./search.html">Search Event</a></li>
      <li><a href="">Account</a></li>
      <li><a href="">Logout</a></li>
      <li><a href="./request.html">Retrieve links</a></li>
      <li><a href="./login.html">Login</a></li>
      <li><a href="./register.html">Create Account</a></li>
    </ul>
  </nav>

  <main>
    <h1>Sign Up</h1>
    <p>Please select one of the available time slots.</p>

    <!--A table with a select drop down for each of the data elements within the table, each table "cell" contains a form element
      which permits the user to individually select time slots for each sheet they wish to be a part of. Divs were used for meaningful spacing.-->

    <form action="./signup.php" method="post">
      <table class="table">
        <tr>
          <td>
            <h3>Sheet Title</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select1"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select1">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>

          <td>
            <h3>3420: Project Check-in</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select2"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select2">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>

          <td>
            <h3>CompSci Tutoring</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select3"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select3">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>

          <td>
            <h3>4000Y Final Presentations</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select4"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select4">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>
        </tr>
      </table>
      <div>
        <input type="submit" value="Confirm Selection" name="submit" class="btn" />
      </div>
    </form>

    <h1>Sign Up - Guest</h1>
    <p>
      Please select one of the available time slots and provide your name and
      email so we can deliver proof of registration.
    </p>

    <!--Similar to the table above, the table below offers a form per cell with the addition of two text boxes for
      inputting a name and email that we will use to deliver proof of registration later on. Divs were used for meaningful spacing.-->

    <form>
      <table class="table">
        <tr>
          <td>
            <h3>Sheet Title</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select5"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select5">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>

          <td>
            <h3>3420: Project Check-in</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select6"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select6">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>

          <td>
            <h3>CompSci Tutoring</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select7"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select7">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>

          <td>
            <h3>4000Y Final Presentations</h3>
            <p><i class="fa-solid fa-user-group"></i> X of Y slots claimed</p>
            <p><i class="fa-solid fa-clock"></i> Next slot time</p>
            <p><i class="fa-solid fa-location-dot"></i> Next slot location</p>
            <label for="select8"><strong>Time Slots:</strong></label>
            <div>
              <select class="time-select" id="select8">
                <option value="">--Please Choose An Option--</option>
                <option value="first-slot">May 30, 11:30 AM</option>
                <option value="second-slot">May 30, 12:40 AM</option>
                <option value="third-slot">May 31, 10:00 AM</option>
                <option value="fourth-slot" disabled>June 1, 9:00 AM</option>
                <option value="fifth-slot" disabled>June 2, 4:00 PM</option>
              </select>
            </div>
          </td>
        </tr>
      </table>
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="John Doe" class="form-control" required />
      </div>

      <div class="form-group">
        <label for="email" class="input-label">Email:</label>
        <input type="email" id="email" name="email" placeholder="frodo@bagend.shire" class="form-control" required />
        <small>We'll never share your email with anyone else.</small>
      </div>

      <div>
        <input type="submit" value="Confirm Selection" name="submit" class="btn" />
      </div>
    </form>
  </main>

  <footer>
    <span>&copy; 2023 - Matthew Makary - <small>Styling replicated with help from neumorphism.io</small></span>
  </footer>
</body>

</html>