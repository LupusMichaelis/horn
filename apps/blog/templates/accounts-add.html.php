          <form action='?add' method='POST' accept-charset='utf-8'>
            <p>
              <input type='submit' value='Add'>
            </p>
			<dl>
              <dt>Name:</dt>
              <dd><input name='account.name[]'
						 value='<?php /*echo $e->a($c['account']->name)*/ ?>'></dd>
			</dl>
			<dl>
              <dt>Email:</dt>
              <dd>
			    <input name='account.email[]'
					   value='<?php /*echo $e->t($c['account']->email)*/ ?>'></dd>
			</dl>
          </form>
