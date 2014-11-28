<style type="text/css">
	div#graph_div {
		width:870px;
		height:300px;
	}
    div.header {
        width:auto;
        line-height:20px;
    }
    	div.header select {
            float:right;
        }
    select, select option {
        font-size:13px;
        padding-left:0px;
    }
</style>

<div class="panel_left">

    <div class="header">
    	<h2>Latest claims</h2>
    </div>

    <div class="item results">

    	<?php if($latest_claims) : ?>

        <table class="results">

            <tr class="order">
                <th>Service provider</th>
                <th>Claim type</th>
                <th>Cost</th>
                <th>Status</th>
            </tr>

            <?php foreach($latest_claims as $claim) : ?>

            <tr class="row no_click">
                <td><?php echo $claim['sp_name']; ?></td>
                <td><?php echo $claim['claim_type']; ?></td>
                <td><?php echo $claim['cost']; ?></td>
                <td><?php echo $claim['status']; ?></td>
            </tr>

            <?php endforeach; ?>

        </table>

        <?php else : ?>

        <p class="no_results">There are no claims</p>

        <?php endif; ?>

    </div>

</div>


<div class="panel_right">

    <div class="header">
    	<h2>Quick links</h2>
    </div>

    <div class="item results">

    	<table class="form quick_links">

        	<tr class="row">
            	<td><img src="/img/icons/add-news.png" alt="Add new" /></td>
                <td>
                	<a href="/admin/news/set">Add news</a>
                    <p>Add a new news item for service providers to view when they log in.</p>
                </td>
            </tr>

            <tr class="row">
            	<td><img src="/img/icons/stats.png" alt="View statistics" /></td>
                <td>
                	<a href="/admin/monitoring-forms/statistics">View statistics</a>
                    <p>View NHS Connecting for Health Information Governance compliant statistics.</p>
                </td>
            </tr>

        </table>

    </div>

</div>


<div class="clear"></div>

<div class="header">

    <select name="range" id="range">
        <option value="month">This month</option>
        <option value="week">This week</option>
        <option value="year">This year</option>
    </select>

    <h2>Total claims</h2>

</div>

<div class="item">

    <div id="graph_div">

        <img src="" alt="" id="graph" />

    </div>

</div>
