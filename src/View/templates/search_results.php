<div class="content-container">
    <div class="header-info">
        <div class="container">
            <img src="assets\img\brasao.png" width="120rem" alt="Brasão do Estado">
        </div>
        <h5>ESTADO DO MARANHÃO<br>SECRETARIA DE ESTADO DA SAÚDE<br>HOSPITAL NINA RODRIGUES</h5>
        <p>Exibindo resultados para <em><u><?php echo strtoupper(htmlspecialchars($viewData['searchTerm'])) ?></u></em></p>
    </div>

    <a href="http://servicos.hnr.ma/arquivo" class="btn-new-search">Nova pesquisa</a>

    <div class="table-container">
        <table id="pacientes" class="table table-striped table-hover <?php echo ($viewData['type'] ?? '') === 'agendamento' ? 'is-agendamento' : 'is-default'; ?>">
            <thead class="table-dark">
            <?php if (count($viewData['results'] ?? [])) : ?>
                <?php if (($viewData['type'] ?? '') === 'agendamento') : ?>
                <tr>
                    <th title="Prontuário"><i class="bi bi-credit-card-2-front"></i></th>
                    <th title="Nome completo">NOME</th>
                    <th title="Data de admissão">ADM.</th>
                    <th title="Hora"><i class="bi bi-clock"></i></th>
                    <th title="Tipo do registro">TIPO</th>
                    <th title="Cartão Nacional de Saúde">CNS</th>
                </tr>
            </thead>
            <tbody>
                <?php else : ?>
                <tr>
                    <th title="Prontuário"><i class="bi bi-credit-card-2-front"></i></th>
                    <th title="Nome completo">NOME</th>
                    <th title="Setor">SETOR</th>
                    <th title="Data de admissão">ADM.</th>
                    <th title="Hora"><i class="bi bi-clock"></i></th>
                    <th title="Data de alta">ALTA</th>
                    <th title="Tipo do registro">TIPO</th>
                    <th title="Cartão Nacional de Saúde">CNS</th>
                </tr>
            </thead>
            <tbody>
                <?php endif; ?>
                <?php foreach (($viewData['results'] ?? []) as $row): ?>
                    <?php if (($viewData['type'] ?? '') === 'agendamento') : ?>
                    <tr>
                        <td title="<?php echo htmlspecialchars($row['PRONTUARIO']) ?>"><?php echo display($row['PRONTUARIO']) ?></td>
                        <td title="<?php echo htmlspecialchars($row['NOME']) ?>"><?php echo display($row['NOME']) ?></td>
                        <td title="<?php echo htmlspecialchars(display_date($row['DTATEND'])) ?>"><?php echo display_date($row['DTATEND']) ?></td>
                        <td title="<?php echo htmlspecialchars(display_date($row['HRATEND'], 'H:i')) ?>"><?php echo display_date($row['HRATEND'], 'H:i') ?></td>
                        <td title="<?php echo htmlspecialchars('AGENDAMENTO') ?>"><?php echo display('AGENDAMENTO') ?></td>
                        <td title="<?php echo htmlspecialchars($row['CNS']) ?>"><?php echo display($row['CNS']) ?></td>
                    </tr>
                    <?php else : ?>
                    <tr>
                        <td title="<?php echo htmlspecialchars($row['PRONTUARIO']) ?>"><?php echo display($row['PRONTUARIO']) ?></td>
                        <td title="<?php echo htmlspecialchars($row['NOME']) ?>"><?php echo display($row['NOME']) ?></td>
                        <td title="<?php echo htmlspecialchars($row['SETOR']) ?>"><?php echo display($row['SETOR']) ?></td>
                        <td title="<?php echo htmlspecialchars(display_date($row['DTATEND'])) ?>"><?php echo display_date($row['DTATEND']) ?></td>
                        <td title="<?php echo htmlspecialchars(display_date($row['HRATEND'], 'H:i')) ?>"><?php echo display_date($row['HRATEND'], 'H:i') ?></td>
                        <td title="<?php echo htmlspecialchars(display_date($row['DTCHECKOUT'])) ?>"><?php echo display_date($row['DTCHECKOUT']) ?></td>
                        <td title="<?php echo htmlspecialchars(strtoupper($viewData['type'])) ?>"><?php echo display(strtoupper($viewData['type'])) ?></td>
                        <td title="<?php echo htmlspecialchars($row['CNS']) ?>"><?php echo display($row['CNS']) ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não foram encontrados resultados.</p>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


