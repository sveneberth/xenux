{{messages}}

<div class="grid-row">
	<section class="box-shadow grid-col">
		#if(new):
			<p><?= __('here can you add a new event') ?></p>
		#else:
			<p><?= __('here can you edit the event') ?></p>
		#endif

		<div class="form">
			{{form}}
		</div>
	</section>
</div>
