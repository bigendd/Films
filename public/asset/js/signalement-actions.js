// public/js/signalement-actions.js

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('archive-button').addEventListener('click', function() {
        let id = this.getAttribute('data-id');

        fetch(`/admin/signalement/${id}/archive`, {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Statut mis à jour');
                location.reload();
            } else {
                alert(data.error);
            }
        });
    });

    document.getElementById('ban-button').addEventListener('click', function() {
        let id = this.getAttribute('data-id');

        fetch(`/admin/signalement/${id}/ban`, {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Utilisateur banni');
                location.reload();
            } else {
                alert(data.error);
            }
        });
    });

    document.getElementById('reply-button').addEventListener('click', function() {
        document.getElementById('response-form').style.display = 'block';
    });

    document.getElementById('send-response').addEventListener('click', function() {
        let id = document.getElementById('reply-button').getAttribute('data-id');
        let responseText = document.getElementById('response-text').value;

        fetch(`/admin/signalement/${id}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'response': responseText,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Réponse envoyée');
                location.reload();
            } else {
                alert(data.error);
            }
        });
    });
});
