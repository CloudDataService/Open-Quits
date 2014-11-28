<div class="functions">
	<a href="/service-providers/staff/add"><img src="/img/btn/add-staff.png" alt="Add staff" /></a>

    <div class="clear"></div>
</div>

<div class="header">
	<h2>Staff members</h2>
</div>

<div class="item results">
    <?php $last_year = date('Y-m-d', strtotime('last year')); ?>
	<?php if($service_provider_staff) : ?>

    <table class="results">

        <tr class="order">
            <th>Name</th>
            <th>Email</th>
            <th>Last login</th>
            <th>Last trained</th>
            <th>Edit</th>
        </tr>

        <?php foreach($service_provider_staff as $sps) : ?>
        <tr class="row">
            <td><?php echo $sps['fname'] . ' ' . $sps['sname']; ?></td>
            <td><?php echo $sps['email']; ?></td>
            <td><?php echo $sps['datetime_last_login_format']; ?></td>
            <td class="<?= ($sps['spst_date'] <= $last_year ? 'training_outdated' : '') ?>"><?php echo $sps['spst_date_format']; ?></td>
            <td><a href="/service-providers/staff/update/<?php echo $sps['id']; ?>"><img src="/img/icons/edit.png" alt="Edit" /></a></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <?php else : ?>

    <p class="no_results">There are currently no staff members linked to your account.</p>

    <?php endif; ?>
</div>
