<?php
			$message.="--> INCLUDE OK\n\n";
			
            $subject = $settings->company_name.' Gift Voucher';
                
            $body .= '<table rules="all" style="border: solid 1px #000; width: 600px;">
                 <tr>
                  <td style="width: 120px;"><strong>Purchased By:</strong></td>
                  <td>'.$voucher_data->name.'( <a href="mailto:'.$voucher_data->email.'">'.$voucher_data->email.'</a> )</td>
                 </tr>
                 <tr>
                  <td><strong>Address:</strong></td>
                  <td>'.$voucher_data->address1.' '.$voucher_data->address2.', '.$voucher_data->city.', '.$voucher_data->postal_code.', '.$voucher_data->state.'</td>
                 </tr>
                 <tr>
                  <td><strong>Telephone:</strong></td>
                  <td>'.$voucher_data->telephone.'</td>
                 </tr>
				 <tr>
                  <td><strong>Delivery Method:</strong></td>
                  <td>'.$voucher_data->delivery_method.'</td>
                 </tr>
                 <tr>
                  <td><strong>Purchased For:</strong></td>
                  <td>'.$voucher_data->recipient_name.'</td>
                 </tr>
                </table><br />';
            
                $body .= '<table style="border: 1px solid black; width: 600px; padding: 5px 20px;">
                 <tr>
                  <td style="text-align: center;">
                        <img src="http://www.mussel-inn.com/giftvoucher/images/email_header.jpg" alt="Mussel Inn - Passionate About Seafood" title="Mussel Inn - Passionate About Seafood" />
                  </td>
                 </tr>
                 <tr>
                  <td>
                                                    
                        <table style="width: 500px;" border="0" cellpadding="0" cellspacing="0">
                         <tr>
                          <td style="width: 100px; text-align: right; vertical-align: bottom;" rowspan="2">
                                To:
                          </td>
                          <td style="font-size: 20px; font-style: italic; text-align: center;">
                               '.$voucher_data->recipient_name.'
                          </td>
                         </tr>
                         <tr>
                          <td>
                              <img src="http://www.mussel-inn.com/giftvoucher/images/email_underline.gif" alt="Mussel Inn Divider Line" title="Mussel Inn Divider Line" />
                          </td>
                         </tr>
                         <tr>
                          <td style="width: 100px; text-align: right; vertical-align: bottom;" rowspan="2">
                                From:
                          </td>
                          <td style="font-size: 20px; font-style: italic; padding-top: 7px; text-align: center;">
                                '.$voucher_data->name.'
                          </td>
                         </tr>
                         <tr>
                          <td>
                              <img src="http://www.mussel-inn.com/giftvoucher/images/email_underline.gif" alt="Mussel Inn Divider Line" title="Mussel Inn Divider Line" />
                          </td>
                         </tr>
                        </table>
                        
                  </td>
                 </tr>
                 <tr>
                  <td style="text-align: center; font: 13px Arial, Helvetica, sans-serif;">
                
                        <br /><br />
                  
                        <span style="font-size: 18px;">This voucher may only be exchanged for food and drink, to be<br />
                        consumed on the premises, in the Mussel Inn at;</span>
                        
                        <br /><br />
                        
                        61-65 Rose Street, Edinburgh EH2 2NH Telephone 0131 225 5979, or<br />
                        157 Hope Street, Glasgow G2 2UQ Telephone 0141 572 1405
                        
                        <br /><br />
                        
                        We recommend when redeeming this voucher that guests telephone<br />
                        the restaurant to book their table in advance.
                        
                        <br /><br />                                        
                
                  </td>
                 </tr>
                 <tr>
                  <td>
                        
                        <table style="width: 500px;" border="0" cellpadding="0" cellspacing="0">
                         <tr>
                          <td style="width: 100px; text-align: right; vertical-align: bottom;" rowspan="2">
                                Amount:
                          </td>
                          <td style="font-size: 20px; font-style: italic; text-align: center;">&pound;
                                '.$voucher_data->voucher_cost.'
                          </td>
                         </tr>
                         <tr>
                          <td>
                                <img src="http://www.mussel-inn.com/giftvoucher/images/email_underline.gif" alt="Mussel Inn Divider Line" title="Mussel Inn Divider Line" />
                          </td>
                         </tr>
                        </table>                                        
                
                  </td>
                 </tr>
                 <tr>
                  <td style="text-align: center;">
                        
                        <table style="width: 500px;" border="0" cellpadding="0" cellspacing="0">
                         <tr>
                          <td style="font: 13px Arial, Helvetica, sans-serif;">
                                http://www.mussel-inn.com<br /><br />
                          </td>
                          <td style="text-align: right;"></td>
                         </tr>
                        </table>
                        
                  </td>
                 </tr>
                </table>';
            
            $body .= '</body></html>';

            $headers = "From: vouchers@mussel-inn.com\r\n";
			$headers .= "Reply-To: vouchers@mussel-inn.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            mail($recipients, $subject, $body, $headers);
?>
