<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Finalización del pago.</title>
    <style>
        .icon {
            display: inline-block;
            width: 7rem;
            height: 7rem;
            margin-bottom: 1rem;
            padding: 2rem;
            border-radius: 50%;
            border: 0.25rem solid;
            -webkit-animation-delay: 1s;
            animation-delay: 1s;
        }

        .confirmation {
            text-align: center;
        }

        .amountIntro {
            color: #8091a5;
        }

        .amount {
            font-size: 1.5rem;
            margin-bottom: 0.1em;
        }

        .amount span {
            font-weight: 600;
        }

        table.transactionInfo {
            font-size: .725rem;
            max-width: 26rem;
            margin: 1.5rem auto;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 4px;
            border-spacing: 0;
        }

        table.transactionInfo tbody tr {
            padding: 0;
        }

        table.transactionInfo tbody tr td.subSection {
            text-align: center;
            background: #fbfbfb;
            color: #1a4594;
        }

        table.transactionInfo tbody tr td:last-child {
            padding-right: 1em;
        }

        table.transactionInfo tbody tr td:first-child {
            padding-left: 1em;
        }

        table.transactionInfo tbody tr td {
            padding: 0.8em 0.5em;
            color: #313541;
            text-align: left;
            font-weight: 700;
            border-bottom: 1px solid #f0f0f0;
            word-break: break-word;
        }

        table.transactionInfo tbody tr td.property {
            color: #667587;
            text-align: right;
            font-weight: 500;
        }

        .asyncPaymentMethodWrapper {
            margin-top: 1rem;
            border-width: 1px 0 0;
            border-style: solid;
            border-color: #f0f0f0;
            padding: 1rem 0;
        }

        .asyncPaymentMethodWrapper .paidWith {
            text-transform: uppercase;
            font-weight: 800;
            font-size: .7rem;
            color: #999;
        }

        .asyncPaymentMethodWrapper .title {
            text-transform: uppercase;
            font-weight: 600;
            color: #1a4594;
        }

        .asyncPaymentMethodWrapper table {
            margin: 1rem auto 0;
            font-size: .9rem;
        }

        .asyncPaymentMethodWrapper table tr:nth-child(odd) {
            background: #fafafa;
        }

        .iconWrapper .icon {
            display: inline-block;
            width: 7rem;
            height: 7rem;
            margin-bottom: 1rem;
            padding: 2rem;
            border-radius: 50%;
            border: 0.25rem solid;
            -webkit-animation-delay: 1s;
            animation-delay: 1s;
        }

        .iconWrapper .icon.approved {
            -webkit-animation: pulse-green 1.5s;
            animation: pulse-green 1.5s;
            border-color: #1c712e;
        }
        .iconWrapper .icon.approved svg path {
            fill: #1c712e;
        }

        .iconWrapper .icon.error {
            border-color: #e34c4c;
            -webkit-animation: pulse-red 1.5s;
            animation: pulse-red 1.5s;
        }
        .iconWrapper .icon.error svg path {
            fill: #e34c4c;
        }

        .view-title {
            font-size: 1.2rem;
            color: #1a4594;
            font-weight: 700;
            margin-bottom: 1em;
            text-align: left;
        }

        body {
            margin: 0;
            background: #f8f8f8;
        }
        .app {
            width: 100%;
            max-width: 30rem;
            margin: 2rem auto 0;
            padding: 2rem 1rem;
            position: relative;
        }
        .contentSection {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .contentSection .contentContainer {
            padding: 1rem;
        }

        @media screen and (min-width: 768px) {
            .app {
                padding: 0;
                max-width: 900px;
                width: 100%;
            }
        }
        @media screen and (max-width: 768px) {
            .app {
                margin: 0 auto;
                padding: 0.5rem;
            }
        }

        @keyframes pulse-green {
            0% {
                -webkit-box-shadow: 0 0 0 0 rgb(28 113 46 / 40%);
                box-shadow: 0 0 0 0 rgb(28 113 46 / 40%);
            }
            70% {
                -webkit-box-shadow: 0 0 0 1em rgb(28 113 46 / 0%);
                box-shadow: 0 0 0 1em rgb(28 113 46 / 0%);
            }
            100% {
                -webkit-box-shadow: 0 0 0 0 rgb(28 113 46 / 0%);
                box-shadow: 0 0 0 0 rgb(28 113 46 / 0%);
            }
        }

        @keyframes pulse-red {
            0% {
                -webkit-box-shadow: 0 0 0 0 rgb(227 76 76 / 40%);
                box-shadow: 0 0 0 0 rgb(227 76 76 / 40%);
            }

            70% {
                -webkit-box-shadow: 0 0 0 1em rgb(227 76 76 / 0%);
                box-shadow: 0 0 0 1em rgb(227 76 76 / 0%);
            }
            100% {
                -webkit-box-shadow: 0 0 0 0 rgb(227 76 76 / 0%);
                box-shadow: 0 0 0 0 rgb(227 76 76 / 0%);
            }
        }

    </style>
</head>
<body>
<div id="app" class="app">
    <div class="contentSection">
        <div class="contentContainer">
            <div class="confirmation" style="">
                <div class="view-title">Resumen de la transacción</div>
                <div>
                    <div class="iconWrapper">
                        @if ( $data->status == "APPROVED")
                            <div class="icon approved">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512">
                                    <path fill="#fff"
                                          d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"></path>
                                </svg>
                            </div>
                        @elseif( $data->status == "ERROR" )
                            <div class="icon error">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512">
                                    <path d="M430.8,502.6l71.8-71.8c12.5-12.5,12.5-32.7,0-45.2L373,256l129.6-129.6c12.5-12.5,12.5-32.7,0-45.2L430.8,9.4
c-12.5-12.5-32.7-12.5-45.2,0L256,139L126.4,9.4c-12.5-12.5-32.7-12.5-45.2,0L9.4,81.2c-12.5,12.5-12.5,32.7,0,45.2L139,256
L9.4,385.6c-12.5,12.5-12.5,32.7,0,45.2l71.8,71.8c12.5,12.5,32.7,12.5,45.2,0L256,373l129.6,129.6C398,515,418.2,515,430.8,502.6z"></path>
                                </svg>
                            </div>
                        @elseif( $data->status == "DECLINED" )
                            <div class="icon error">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 512 512">
                                    <path d="M336,432c0,44.1-35.9,80-80,80s-80-35.9-80-80s35.9-80,80-80S336,387.9,336,432z M185.3,25.2l13.6,272
c0.6,12.8,11.2,22.8,24,22.8h66.3c12.8,0,23.3-10,24-22.8l13.6-272c0.7-13.7-10.2-25.2-24-25.2h-93.5C195.5,0,184.6,11.5,185.3,25.2
z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                @if ( $data->status == "APPROVED")
                    <div class="amountIntro">Pagaste:</div>
                    <div class="amount"><span>{{$data->currency}} $ {{$data->amount_in_cents/100}}</span></div>
                @elseif( $data->status == "ERROR" )
                    <div class="amountIntro">Monto:</div>
                    <div class="amount"><span>{{$data->currency}} $ {{$data->amount_in_cents/100}}</span></div>
                @elseif( $data->status == "DECLINED" )
                    <div class="amountIntro">Se declino la transacción</div>
                    <div class="amount"><span>{{$data->currency}} $ {{$data->amount_in_cents/100}}</span></div>
                @endif
                @if ( $data->status_message != null )
                    <div class="amountIntro">{{ $data->status_message  }}</div>
                @endif
            </div>
            <table class="transactionInfo">
                <tbody>
                <tr>
                    <td class="subSection" colspan="2">Información de la transacción</td>
                </tr>
                <tr>
                    <td class="property">Transacción #</td>
                    <td>{{$data->id}}</td>
                </tr>
                <tr>
                    <td class="property">Referencia</td>
                    <td>{{$data->reference}}</td>
                </tr>
                </tbody>
            </table>
            <div class="asyncPaymentMethodWrapper confirmation">
                <div class="paidWith">Pago efectuado con</div>
                <div class="title">Transferencia con Botón Bancolombia</div>
                <table cellpadding="5">
                    <tr>
                        <td>Pago efectuado a:</td>
                        <td>{{$data->merchant->name}} -
                            ({{$data->merchant->legal_name}} {{$data->merchant->legal_id_type}} {{$data->merchant->legal_id}}
                            )
                        </td>
                    </tr>
                    <tr>
                        <td>Descripción del pago:</td>
                        <td>Pago a {{$data->merchant->name}}, ref: {{$data->reference}}</td>
                    </tr>
                </table>
                <br><br>
                <div class="print hideForPrinting">Imprimir comprobante</div>
            </div>
            <br>
            <div class="confirmation">
                <button href="/">Regresar Blue Comunicaciones</button>
            </div>
            <br><br></div>
    </div>
</div>
</div>
</body>
</html>
