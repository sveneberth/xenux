{{messages}}

<div class="grid-row">
		<section class="box-shadow grid-col">
		#if(new):
			<p><?= __('here can you add a new user') ?></p>
		#else:
			<?php
			if($profileEdit)
				echo '<p>' . __('here can you edit your profile') . '</p>';
			else
				echo '<p>' . __('here can you edit the user') . '</p>';
			?>
		#endif

		<div class="form">
			{{form}}
		</div>
	</section>
</div>
