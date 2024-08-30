<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportação de Usuários</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4A90E2;
            font-size: 28px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #4A90E2;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .role-badge {
            display: inline-block;
            background-color: #4A90E2;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Relatório de Usuários</h1>
    <table>
        <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Email Verificado em</th>
            <th>Criado em</th>
            <th>Atualizado em</th>
        </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $record->name }}</td>
                <td>{{ $record->email }}</td>
                <td>
                    @foreach($record->roles as $role)
                        <span class="role-badge">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td>{{ $record->email_verified_at ? $record->email_verified_at->format('d/m/Y H:i') : 'Não Verificado' }}</td>
                <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $record->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="footer">
        <p>Relatório gerado em {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>
</body>
</html>
