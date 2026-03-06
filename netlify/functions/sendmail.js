const sgMail = require('@sendgrid/mail');

exports.handler = async function(event, context) {
  if (event.httpMethod !== 'POST') {
    return { statusCode: 405, body: 'Method Not Allowed' };
  }

  try {
    const { name, email, phone, project_type, budget } = JSON.parse(event.body);
    
    sgMail.setApiKey(process.env.SENDGRID_API_KEY);

    const msg = {
      to: ['bogdan.epure@sky.ro', 'sorin.pintilie@sky.ro'],
      from: 'sorin.pintilie@sky.ro',
      subject: 'Nouă cerere de ofertă de pe Sky.ro',
      html: `
        <html>
        <head><title>Nouă Cerere de Proiect - Sky.ro</title></head>
        <body>
          <h2>Nouă cerere de proiect de pe Sky.ro</h2>
          <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
            <tr><td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Nume</td><td style='padding: 10px; border: 1px solid #ddd;'>${name}</td></tr>
            <tr><td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Email</td><td style='padding: 10px; border: 1px solid #ddd;'>${email}</td></tr>
            <tr><td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Telefon</td><td style='padding: 10px; border: 1px solid #ddd;'>${phone}</td></tr>
            <tr><td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Tip Proiect</td><td style='padding: 10px; border: 1px solid #ddd;'>${project_type}</td></tr>
            <tr><td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Buget</td><td style='padding: 10px; border: 1px solid #ddd;'>${budget}</td></tr>
          </table>
        </body>
        </html>
      `,
    };

    await sgMail.send(msg);
    return { statusCode: 200, body: 'Email trimis cu succes!' };
  } catch (error) {
    console.error('Error sending email:', error);
    return { statusCode: 500, body: 'Eroare la trimiterea emailului: ' + error.message };
  }
};
