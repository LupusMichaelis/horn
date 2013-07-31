          <form action='?edit' method='POST' accept-charset='utf-8'>
            <p>
              <input type='submit' value='Update'>
            </p>
            <table>
              <tr>
                <td>Title:</td>
                <td><input name='' value='<?php echo $e->a($c->title) ?>'></td>
              </tr>
              <tr>
                <td>Description:</td>
                <td><input name='' value='<?php echo $e->t($c->description) ?>'></td>
              </tr>
            </table>
          </form>
