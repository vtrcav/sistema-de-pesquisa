$(document).ready(function () {
    // Use os dados passados do PHP
    const excelTitle = `Resultados_${pageData.searchTerm}_${pageData.currentDate}`;
    const printTitle = `Resultados para ${pageData.searchTerm}`;

    $('#pacientes').DataTable({
        language: {
            lengthMenu: '_MENU_ registros por página',
            zeroRecords: 'Nenhum registro encontrado',
            info: 'Página _PAGE_ de _PAGES_',
            infoEmpty: 'Não há registros disponíveis',
            infoFiltered: '(filtrado de _MAX_ registros)',
            search: 'Pesquisar:',
            paginate: {
                first: 'Primeiro',
                last: 'Último',
                next: 'Próximo',
                previous: 'Anterior'
            }
        },
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Exportar para Excel',
                title: excelTitle,
                className: 'export-excel',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: 'Imprimir',
                title: printTitle,
                className: 'print-button',
                customize: function (win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    
                    // Adiciona o brasão e cabeçalho antes do título
                    $(win.document.body).prepend(
                        '<div style="text-align:center; margin-bottom: 20px;">' +
                        '<img src="assets/img/brasao.png" width="100" style="margin-bottom: 10px;"><br>' +
                        '<strong style="font-size: 16px;">ESTADO DO MARANHÃO<br>' +
                        'SECRETARIA DE ESTADO DA SAÚDE<br>' +
                        'HOSPITAL NINA RODRIGUES</strong>' +
                        '</div>'
                    );
                    
                    // Adicionar rodapé
                    $(win.document.body).append(
                        '<div style="text-align:center; margin-top: 20px; font-size: 12px; color: #666;">' +
                        'Desenvolvido e mantido pelo CPD - Centro de Processamento de Dados' +
                        '</div>'
                    );
                    
                    // Estilo da tabela
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '12px')
                        .css('border-collapse', 'collapse')
                        .css('width', '100%');
                        
                    $(win.document.body).find('table th')
                        .css('background-color', '#1a4da2')
                        .css('color', 'white')
                        .css('padding', '8px')
                        .css('border', '1px solid #ddd');
                        
                    $(win.document.body).find('table td')
                        .css('padding', '8px')
                        .css('border', '1px solid #ddd');
                        
                    $(win.document.body).find('table tr:nth-child(even)')
                        .css('background-color', '#f2f2f2');
                },
                exportOptions: {
                    columns: ':visible'
                }
            }
        ]
    });
});

window.onload = function () {
    setTimeout(function() {
        $(".loader").fadeOut(500, function () {
            $(".conteudo").fadeIn(500);
        });
    }, 800);
};