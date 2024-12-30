<div id="accordion">
  <div class="card">
    <h3 class="text-center"><?php echo get_option('companyname'); ?></h3>
    <h4 class="text-center"><?php echo _l('balance_sheet_comparison'); ?></h4>
    <p class="text-center"><?php echo _d($data_report['from_date']) .' - '. _d($data_report['to_date']); ?></p>
    <table class="tree">
      <thead>
        <tr class="tr_header">
          <th rowspan="2"></th>
          <th colspan="2" class="text-center th_total"><?php echo _l('total'); ?></th>
        </tr>
        <tr class="tr_header">
          <th class="th_total"><?php echo html_entity_decode($data_report['this_year']); ?></th>
          <th class="th_total"><?php echo html_entity_decode($data_report['last_year']); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr class="treegrid-1000 parent-node expanded">
          <td class="parent"><?php echo _l('acc_assets'); ?></td>
          <td></td>
        </tr>
        <?php
          $row_index = 0;
          $parent_index = 0;
          $row_index += 1;
          $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> treegrid-parent-1000 parent-node expanded">
            <td class="parent"><?php echo _l('acc_current_assets'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php
          $row_index += 1;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
            <td class="parent"><?php echo _l('acc_accounts_receivable'); ?></td>
            <td></td>
            <td></td>
          </tr>
          <?php 
            $_index = $row_index;
            foreach ($data_report['data']['accounts_receivable'] as $key => $value) { 
              $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($_index); ?>">
              <td>
              <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
          <?php }
            $row_index += 1;
           ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_accounts_receivable'); ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['accounts_receivable']['this_year'], $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['accounts_receivable']['last_year'], $currency->name); ?> </td>
          </tr>
          <?php foreach ($data_report['data']['cash_and_cash_equivalents'] as $key => $value) {
            $row_index += 1;
           ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td>
              <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
          <?php } 
            $row_index += 1;
          ?>
          <?php foreach ($data_report['data']['current_assets'] as $key => $value) { 
            $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td>
              <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
          <?php } 
            $row_index += 1;
          ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-1000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_current_assets'); ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['current_assets']['this_year'] + $data_report['total']['cash_and_cash_equivalents']['this_year'] + $data_report['total']['accounts_receivable']['this_year'], $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['current_assets']['last_year'] + $data_report['total']['cash_and_cash_equivalents']['last_year'] + $data_report['total']['accounts_receivable']['last_year'], $currency->name); ?> </td>
          </tr>
          <?php 
            $row_index += 1;
            $parent_index = $row_index;
          ?>
          <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> treegrid-parent-1000 parent-node expanded">
            <td class="parent"><?php echo _l('long_term_assets'); ?></td>
            <td></td>
          </tr>
          <?php foreach ($data_report['data']['fixed_assets'] as $key => $value) { 
            $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td>
                <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
                <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
            <?php } ?>
            <?php foreach ($data_report['data']['non_current_assets'] as $key => $value) { 
            $row_index += 1;
              ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td >
                <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
                <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
            <?php } 
            $row_index += 1;
            ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-1000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_long_term_assets'); ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['fixed_assets']['this_year']+ $data_report['total']['non_current_assets']['this_year'], $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['fixed_assets']['last_year']+ $data_report['total']['non_current_assets']['last_year'], $currency->name); ?> </td>
          </tr>
          <?php 
            $row_index += 1;
            ?>
          <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> tr_total">
            <td class="parent"><?php echo _l('total_assets'); ?></td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['fixed_assets']['this_year']+ $data_report['total']['non_current_assets']['this_year'] + $data_report['total']['current_assets']['this_year'] + $data_report['total']['cash_and_cash_equivalents']['this_year'] + $data_report['total']['accounts_receivable']['this_year'], $currency->name); ?> </td>
            <td class="total_amount"><?php echo app_format_money($data_report['total']['fixed_assets']['last_year']+ $data_report['total']['non_current_assets']['last_year'] + $data_report['total']['current_assets']['last_year'] + $data_report['total']['cash_and_cash_equivalents']['last_year'] + $data_report['total']['accounts_receivable']['last_year'], $currency->name); ?> </td>
          </tr>
          <?php 
            $row_index += 1;
            ?>
            <tr class="treegrid-1001 parent-node expanded">
              <td class="parent"><?php echo _l('liabilities_and_shareholders_equity'); ?></td>
              <td></td>
            </tr>
            <?php
            $row_index += 1;
            $parent_index = $row_index;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> treegrid-parent-1001 parent-node expanded">
              <td class="parent"><?php echo _l('acc_current_liabilities'); ?></td>
              <td></td>
            </tr>
            <?php $row_index += 1; ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> parent-node expanded">
              <td class="parent"><?php echo _l('accounts_payable'); ?></td>
              <td></td>
            </tr>
            <?php 
              $_index = $row_index;
              foreach ($data_report['data']['accounts_payable'] as $key => $value) { 
                $row_index += 1;
              ?>
              <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($_index); ?>">
                <td>
                <?php echo html_entity_decode($value['name']); ?> 
                </td>
                <td class="total_amount">
                <?php echo html_entity_decode($value['amount']); ?> 
                </td>
                <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
              </tr>
            <?php } $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?> tr_total">
              <td class="parent"><?php echo _l('total_accounts_payable'); ?></td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['accounts_payable']['this_year'], $currency->name); ?> </td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['accounts_payable']['last_year'], $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
            <?php foreach ($data_report['data']['credit_card'] as $key => $value) { 
              $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td >
                <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
                <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
            <?php } $row_index += 1; ?>
            <?php foreach ($data_report['data']['current_liabilities'] as $key => $value) { 
              $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td >
                <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
                <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
            <?php } $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-1001 tr_total">
              <td class="parent"><?php echo _l('total_current_liabilities'); ?></td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['credit_card']['this_year'] + $data_report['total']['current_liabilities']['this_year'] + $data_report['total']['accounts_payable']['this_year'], $currency->name); ?> </td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['credit_card']['last_year'] + $data_report['total']['current_liabilities']['last_year'] + $data_report['total']['accounts_payable']['last_year'], $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
            <?php
            $row_index += 1;
            $parent_index = $row_index;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> treegrid-parent-1001 parent-node expanded">
              <td class="parent"><?php echo _l('acc_non_current_liabilities'); ?></td>
              <td></td>
            </tr>
            <?php $row_index += 1; ?>
            <?php foreach ($data_report['data']['non_current_liabilities'] as $key => $value) { 
              $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td >
                <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
                <?php echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
            <?php } $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-1001 tr_total">
              <td class="parent"><?php echo _l('total_non_current_liabilities'); ?></td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['non_current_liabilities']['this_year'], $currency->name); ?> </td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['non_current_liabilities']['last_year'], $currency->name); ?> </td>
            </tr>

            <?php $row_index += 1; ?>
            <?php
            $row_index += 1;
            $parent_index = $row_index;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> treegrid-parent-1001 parent-node expanded">
              <td class="parent"><?php echo _l('shareholders_equity'); ?></td>
              <td></td>
            </tr>
            <?php $row_index += 1; ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td >
                <?php echo _l('acc_net_income'); ?> 
              </td>
              <td class="total_amount">
                <?php echo app_format_money($data_report['this_net_income'], $currency->name); ?> 
              </td>
              <td class="total_amount">
              <?php echo app_format_money($data_report['last_net_income'], $currency->name); ?> 
              </td>
            </tr>
            <?php $row_index += 1; ?>
            <?php foreach ($data_report['data']['owner_equity'] as $key => $value) { 
              $row_index += 1;
            ?>
            <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-<?php echo html_entity_decode($parent_index); ?>">
              <td >
                <?php echo html_entity_decode($value['name']); ?> 
              </td>
              <td class="total_amount">
                <?php 
                echo html_entity_decode($value['amount']); ?> 
              </td>
              <td class="total_amount">
              <?php echo html_entity_decode($value['py_amount']); ?> 
              </td>
            </tr>
            <?php } $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-1001 tr_total">
              <td class="parent"><?php echo _l('total_shareholders_equity'); ?></td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['owner_equity']['this_year'] + $data_report['this_net_income'], $currency->name); ?> </td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['owner_equity']['last_year'] + $data_report['last_net_income'], $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
            <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> tr_total">
              <td class="parent"><?php echo _l('total_liabilities_and_equity'); ?></td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['owner_equity']['this_year']+ $data_report['this_net_income']+ $data_report['total']['non_current_liabilities']['this_year'] + $data_report['total']['credit_card']['this_year'] + $data_report['total']['accounts_payable']['this_year'] + $data_report['total']['current_liabilities']['this_year'], $currency->name); ?> </td>
              <td class="total_amount"><?php echo app_format_money($data_report['total']['owner_equity']['last_year']+ $data_report['last_net_income']+ $data_report['total']['non_current_liabilities']['last_year'] + $data_report['total']['credit_card']['last_year'] + $data_report['total']['accounts_payable']['last_year'] + $data_report['total']['current_liabilities']['last_year'], $currency->name); ?> </td>
            </tr>
            <?php $row_index += 1; ?>
        </tbody>
    </table>
  </div>
</div>