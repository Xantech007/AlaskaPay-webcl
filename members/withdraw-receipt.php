<style>

.receipt-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    border:1px solid #eef2f7;
}

.receipt-header{
    background:linear-gradient(135deg,#001f3f,#003366);
    color:#fff;
    text-align:center;
    padding:35px 25px;
}

.receipt-header i{
    font-size:70px;
    margin-bottom:15px;
    color:#4db8ff;
}

.receipt-header h2{
    margin:0;
    font-size:30px;
    font-weight:700;
}

.receipt-header p{
    margin-top:8px;
    opacity:.85;
}

.receipt-body{
    padding:30px;
}

.receipt-table{
    width:100%;
    border-collapse:collapse;
}

.receipt-table tr{
    border-bottom:1px solid #f0f2f5;
}

.receipt-table tr:last-child{
    border-bottom:none;
}

.receipt-table td{
    padding:16px 0;
}

.receipt-table td:first-child{
    color:#555;
    font-weight:600;
}

.receipt-table td:last-child{
    text-align:right;
    font-weight:700;
    color:#2c3e50;
}

.receipt-id{
    color:#3498db;
    font-size:18px;
}

.amount-highlight{
    font-size:24px;
    color:#27ae60;
    font-weight:700;
}

.converted-amount{
    font-size:20px;
    color:#9b59b6;
    font-weight:700;
}

.status-badge{
    display:inline-block;
    padding:8px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.receipt-footer{
    background:#f8fbff;
    padding:25px;
    border-top:1px solid #eef2f7;
}

.receipt-actions{
    display:flex;
    gap:15px;
}

.receipt-actions a{
    flex:1;
    text-decoration:none;
    text-align:center;
}

.receipt-note{
    margin-top:20px;
    background:#f8fbff;
    border-left:4px solid #3498db;
    padding:15px;
    border-radius:10px;
    color:#555;
    font-size:14px;
}

@media(max-width:768px){

    .receipt-body{
        padding:20px;
    }

    .receipt-table td{
        display:block;
        width:100%;
        text-align:left !important;
        padding:8px 0;
    }

    .receipt-table tr{
        display:block;
        padding:12px 0;
    }

    .receipt-actions{
        flex-direction:column;
    }

    .receipt-header h2{
        font-size:24px;
    }

    .receipt-header i{
        font-size:55px;
    }

    .amount-highlight{
        font-size:20px;
    }

}
</style>


<div class="loan-form">

    <div class="receipt-card">

        <div class="receipt-header">

            <i class="fas fa-receipt"></i>

            <h2>Withdrawal Receipt</h2>

            <p>
                Transaction successfully recorded
            </p>

        </div>

        <div class="receipt-body">

            <table class="receipt-table">

                <tr>
                    <td>Receipt ID</td>
                    <td class="receipt-id">
                        #<?= $withdrawal['id'] ?>
                    </td>
                </tr>

                <tr>
                    <td>Amount (USD)</td>
                    <td class="amount-highlight">
                        $<?= number_format($withdrawal['amount'], 2) ?>
                    </td>
                </tr>

                <?php if (!empty($withdrawal['receive_currency'])): ?>

                <tr>
                    <td>
                        Amount (<?= htmlspecialchars($withdrawal['receive_currency']) ?>)
                    </td>
                    <td class="converted-amount">
                        <?= htmlspecialchars($withdrawal['receive_currency']) ?>
                        <?= number_format($withdrawal['receive_amount'], 2) ?>
                    </td>
                </tr>

                <tr>
                    <td>Exchange Rate</td>
                    <td>
                        <?= number_format($withdrawal['exchange_rate'], 2) ?>
                    </td>
                </tr>

                <?php endif; ?>

                <tr>
                    <td>Payment Method</td>
                    <td>
                        <?= htmlspecialchars($withdrawal['method']) ?>
                    </td>
                </tr>

                <tr>
                    <td>Account Name</td>
                    <td>
                        <?= htmlspecialchars($withdrawal['account_name']) ?>
                    </td>
                </tr>

                <tr>
                    <td>Account ID</td>
                    <td>
                        <?= htmlspecialchars($withdrawal['account_id']) ?>
                    </td>
                </tr>

                <tr>
                    <td>Status</td>
                    <td>

                        <span
                            class="status-badge"
                            style="
                                background:<?= $statusColor ?>22;
                                color:<?= $statusColor ?>;
                            ">
                            <?= ucfirst($withdrawal['status']) ?>
                        </span>

                    </td>
                </tr>

                <tr>
                    <td>Date & Time</td>
                    <td>
                        <?= date(
                            'd M Y h:i A',
                            strtotime($withdrawal['created_at'])
                        ) ?>
                    </td>
                </tr>

            </table>

            <div class="receipt-note">
                <i class="fas fa-info-circle"></i>
                Your withdrawal request has been submitted successfully.
                Processing time may vary depending on your selected payment method.
            </div>

        </div>

        <div class="receipt-footer">

            <div class="receipt-actions">

                <a href="dashboard"
                   class="submit-btn">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>

                <a href="withdrawal-history"
                   class="submit-btn"
                   style="background:#555;">
                    <i class="fas fa-history"></i>
                    Withdrawal History
                </a>

            </div>

        </div>

    </div>

</div>
