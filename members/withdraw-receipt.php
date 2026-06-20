div class="loan-form">

    <div style="text-align:center;margin-bottom:25px;">

        <i class="fas fa-receipt"
           style="
                font-size:60px;
                color:#3498db;
                margin-bottom:10px;
           ">
        </i>

        <h2>Withdrawal Receipt</h2>

        <p style="color:#777;">
            Transaction successfully recorded
        </p>

    </div>

    <div style="
        background:#fff;
        border-radius:12px;
        padding:20px;
        border:1px solid #eee;
    ">

        <table style="width:100%;border-collapse:collapse;">

            <tr>
                <td style="padding:12px 0;">
                    <strong>Receipt ID</strong>
                </td>
                <td style="text-align:right;">
                    #<?= $withdrawal['id'] ?>
                </td>
            </tr>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Amount (USD)</strong>
                </td>
                <td style="text-align:right;">
                    $<?= number_format($withdrawal['amount'], 2) ?>
                </td>
            </tr>

            <?php if (!empty($withdrawal['receive_currency'])): ?>

            <tr>
                <td style="padding:12px 0;">
                    <strong>
                        Amount (<?= htmlspecialchars($withdrawal['receive_currency']) ?>)
                    </strong>
                </td>
                <td style="text-align:right;">
                    <?= htmlspecialchars($withdrawal['receive_currency']) ?>
                    <?= number_format($withdrawal['receive_amount'], 2) ?>
                </td>
            </tr>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Exchange Rate</strong>
                </td>
                <td style="text-align:right;">
                    <?= number_format($withdrawal['exchange_rate'], 2) ?>
                </td>
            </tr>

            <?php endif; ?>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Method</strong>
                </td>
                <td style="text-align:right;">
                    <?= htmlspecialchars($withdrawal['method']) ?>
                </td>
            </tr>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Account Name</strong>
                </td>
                <td style="text-align:right;">
                    <?= htmlspecialchars($withdrawal['account_name']) ?>
                </td>
            </tr>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Account ID</strong>
                </td>
                <td style="text-align:right;">
                    <?= htmlspecialchars($withdrawal['account_id']) ?>
                </td>
            </tr>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Status</strong>
                </td>
                <td style="text-align:right;">

                    <span style="
                        padding:6px 12px;
                        border-radius:20px;
                        background:<?= $statusColor ?>22;
                        color:<?= $statusColor ?>;
                        font-weight:600;
                    ">
                        <?= ucfirst($withdrawal['status']) ?>
                    </span>

                </td>
            </tr>

            <tr>
                <td style="padding:12px 0;">
                    <strong>Date</strong>
                </td>
                <td style="text-align:right;">
                    <?= date(
                        'd M Y h:i A',
                        strtotime($withdrawal['created_at'])
                    ) ?>
                </td>
            </tr>

        </table>

    </div>

    <div style="
        margin-top:25px;
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    ">

        <a href="dashboard.php"
           class="submit-btn"
           style="
                text-decoration:none;
                text-align:center;
                flex:1;
           ">
            Back To Dashboard
        </a>

        <a href="history.php"
           class="submit-btn"
           style="
                text-decoration:none;
                text-align:center;
                flex:1;
                background:#555;
           ">
            View History
        </a>

    </div>

</div>
